<?php

use App\ActivityAction;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\StageProgress;
use App\Models\Template;
use App\Models\User;
use App\Services\ProjectGeneratorService;
use Database\Seeders\StageSeeder;
use Database\Seeders\TemplateSeeder;

beforeEach(function () {
    // الـ layout يستخدم @vite، ما نحتاج ملفات مبنية في الاختبار
    $this->withoutVite();

    $this->seed(TemplateSeeder::class);
    $this->seed(StageSeeder::class);
});

function pUser(array $attributes = []): User
{
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $user->forceFill(array_merge([
        'role' => 'learner',
        'status' => 'active',
    ], $attributes))->save();

    return $user;
}

function pTemplate(string $slug, bool $active): Template
{
    return Template::create([
        'name' => ucfirst($slug),
        'slug' => $slug,
        'description' => 'Test template',
        'is_active' => $active,
    ]);
}

// ───────────────────────── Access & Middleware ─────────────────────────

it('redirects guests to login when accessing project routes', function () {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
    $this->get(route('projects.create'))->assertRedirect(route('login'));
    $this->post(route('projects.store'), [])->assertRedirect(route('login'));
});

it('forbids admins from accessing learner project routes', function () {
    $admin = pUser(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('projects.index'))
        ->assertForbidden();
});

it('blocks suspended learners from accessing project routes', function () {
    $suspended = pUser(['status' => 'suspended']);

    $response = $this->actingAs($suspended)->get(route('projects.index'));

    // EnsureAccountActive قد يرجع 403 أو يحول لصفحة الدخول، الاثنان يمنعان الوصول
    expect($response->status())->toBeIn([302, 403]);
    expect($response->status())->not->toBe(200);
});

// ───────────────────────── Projects Index ─────────────────────────

it('allows an active learner to view the projects index page', function () {
    $learner = pUser();

    $this->actingAs($learner)
        ->get(route('projects.index'))
        ->assertOk();
});

it('only displays projects owned by the authenticated learner', function () {
    $learnerA = pUser();
    $learnerB = pUser();
    $template = Template::active()->first();

    $generator = app(ProjectGeneratorService::class);
    $projectA = $generator->generate($learnerA, $template, 'Learner A Project');
    $projectB = $generator->generate($learnerB, $template, 'Learner B Project');

    $response = $this->actingAs($learnerA)
        ->get(route('projects.index'))
        ->assertOk();

    $projectsInView = $response->viewData('projects');

    expect($projectsInView)->toHaveCount(1)
        ->and($projectsInView->first()->id)->toBe($projectA->id)
        ->and($projectsInView->contains('id', $projectB->id))->toBeFalse();
});

// ───────────────────────── Projects Create ─────────────────────────

it('displays the project creation page with active templates', function () {
    $learner = pUser();

    $response = $this->actingAs($learner)
        ->get(route('projects.create'))
        ->assertOk();

    $templatesInView = $response->viewData('templates');

    expect($templatesInView->pluck('slug')->toArray())
        ->toContain('portfolio', 'landing-page');
});

it('does not display inactive templates on create page', function () {
    $learner = pUser();

    pTemplate('hidden-template', false);

    $response = $this->actingAs($learner)
        ->get(route('projects.create'))
        ->assertOk();

    $templatesInView = $response->viewData('templates');

    expect($templatesInView->pluck('slug')->toArray())
        ->not->toContain('hidden-template');
});

// ───────────────────────── Projects Store ─────────────────────────

it('creates a project, initializes stages, and logs the activity', function () {
    $learner = pUser();
    $template = Template::where('slug', 'portfolio')->firstOrFail();

    $response = $this->actingAs($learner)
        ->post(route('projects.store'), [
            'template_id' => $template->id,
            'title' => 'My Personal Portfolio',
        ]);

    $project = Project::where('title', 'My Personal Portfolio')->first();

    expect($project)->not->toBeNull()
        ->and($project->user_id)->toBe($learner->id)
        ->and($project->template_id)->toBe($template->id)
        ->and($project->status)->toBe('in_progress');

    $response->assertRedirect(route('projects.show', $project))
        ->assertSessionMissing('status');

    // Stage 0 العامة + 6 مراحل بورتفوليو = 7
    $progressRecords = StageProgress::where('project_id', $project->id)
        ->with('stage')
        ->get()
        ->sortBy('stage.order')
        ->values();

    expect($progressRecords)->toHaveCount(7)
        ->and($progressRecords->first()->status)->toBe('in_progress')
        ->and($project->current_stage_id)->toBe($progressRecords->first()->stage_id);

    // باقي المراحل مقفولة
    for ($i = 1; $i < $progressRecords->count(); $i++) {
        expect($progressRecords[$i]->status)->toBe('locked');
    }

    // سجل النشاط
    $log = ActivityLog::where('user_id', $learner->id)
        ->where('action', ActivityAction::ProjectCreated->value)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->description)->toBe("Created project: {$project->title}");
});

it('returns a JSON response when storing a project with JSON request', function () {
    $learner = pUser();
    $template = Template::where('slug', 'portfolio')->firstOrFail();

    $response = $this->actingAs($learner)
        ->postJson(route('projects.store'), [
            'template_id' => $template->id,
            'title' => 'API Generated Project',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'API Generated Project')
        ->assertJsonPath('data.status', 'in_progress')
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'status',
                'current_stage_id',
                'created_at',
                'template',
                'stages',
            ],
        ]);
});

it('rejects project creation when title is missing', function () {
    $learner = pUser();
    $template = Template::active()->first();

    $this->actingAs($learner)
        ->post(route('projects.store'), [
            'template_id' => $template->id,
            'title' => '',
        ])
        ->assertSessionHasErrors('title');

    expect(Project::count())->toBe(0);
});

it('rejects project creation when template_id is invalid or inactive', function () {
    $learner = pUser();

    // قالب غير موجود
    $this->actingAs($learner)
        ->post(route('projects.store'), [
            'template_id' => 99999,
            'title' => 'Invalid Template Project',
        ])
        ->assertSessionHasErrors('template_id');

    // قالب غير مفعّل
    $inactive = pTemplate('draft-template', false);

    $this->actingAs($learner)
        ->post(route('projects.store'), [
            'template_id' => $inactive->id,
            'title' => 'Inactive Template Project',
        ])
        ->assertSessionHasErrors('template_id');

    expect(Project::count())->toBe(0);
});

// ───────────────────────── Projects Show ─────────────────────────

it('allows a learner to view their own project', function () {
    $learner = pUser();
    $template = Template::active()->first();
    $project = app(ProjectGeneratorService::class)->generate($learner, $template, 'My Own Project');

    $this->actingAs($learner)
        ->get(route('projects.show', $project))
        ->assertOk();
});

it('forbids a learner from viewing another learners project', function () {
    $learnerA = pUser();
    $learnerB = pUser();
    $template = Template::active()->first();

    $projectB = app(ProjectGeneratorService::class)->generate($learnerB, $template, 'Learner B Project');

    $this->actingAs($learnerA)
        ->get(route('projects.show', $projectB))
        ->assertForbidden();
});

// ───────────────────────── Projects Destroy ─────────────────────────

it('allows a learner to delete their own project and logs the activity', function () {
    $learner = pUser();
    $template = Template::active()->first();
    $project = app(ProjectGeneratorService::class)->generate($learner, $template, 'Project To Delete');

    $projectId = $project->id;
    $projectTitle = $project->title;

    $this->actingAs($learner)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'))
        ->assertSessionMissing('status');

    $this->assertDatabaseMissing('projects', ['id' => $projectId]);

    $log = ActivityLog::where('user_id', $learner->id)
        ->where('action', ActivityAction::ProjectDeleted->value)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->description)->toBe("Deleted project: {$projectTitle}");
});

it('forbids a learner from deleting another learners project', function () {
    $learnerA = pUser();
    $learnerB = pUser();
    $template = Template::active()->first();

    $projectB = app(ProjectGeneratorService::class)->generate($learnerB, $template, 'Learner B Project');

    $this->actingAs($learnerA)
        ->delete(route('projects.destroy', $projectB))
        ->assertForbidden();

    $this->assertDatabaseHas('projects', ['id' => $projectB->id]);
});
