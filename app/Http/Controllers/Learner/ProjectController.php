<?php

namespace App\Http\Controllers\Learner;

use App\ActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Template;
use App\Services\ActivityLogService;
use App\Services\ProjectGeneratorService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectGeneratorService $generator,
        private ActivityLogService $activityLog,
    ) {}

    /**
     * قائمة مشاريع المتعلم.
     */
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->with('template')
            ->withCount([
                'stageProgress',
                'stageProgress as completed_stages_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->latest()
            ->get();

        return view('learner.projects.index', compact('projects'));
    }

    /**
     * صفحة اختيار القالب.
     */
    public function create()
    {
        $templates = Template::active()->withCount('stages')->get();

        return view('learner.projects.create', compact('templates'));
    }

    /**
     * إنشاء مشروع جديد.
     * البيانات تم التحقق منها في StoreProjectRequest.
     */
    public function store(StoreProjectRequest $request)
    {
        $template = Template::findOrFail($request->validated('template_id'));

        $project = $this->generator->generate(
            $request->user(),
            $template,
            $request->validated('title'),
        );

        // إذا الطلب JSON (مثلاً من AJAX) نرجع Resource
        if ($request->expectsJson()) {
            return (new ProjectResource($project->load(['template', 'stageProgress.stage'])))
                ->response()
                ->setStatusCode(201);
        }

        return redirect()
            ->route('projects.show', $project);
    }

    /**
     * عرض مشروع واحد مع مراحله.
     * Route Model Binding يجيب المشروع، والـ Policy تتأكد إنه ملك المتعلم.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['template', 'stageProgress.stage']);

        $stages = $project->stageProgress
            ->sortBy('stage.order')
            ->values();

        return view('learner.projects.show', compact('project', 'stages'));
    }

    /**
     * حذف مشروع.
     */
    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $title = $project->title;
        $project->delete();

        $this->activityLog->log(
            ActivityAction::ProjectDeleted,
            $request->user(),
            "Deleted project: {$title}"
        );

        return redirect()
            ->route('projects.index');
    }
}
