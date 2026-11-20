<?php

namespace App\Services;

use App\ActivityAction;
use App\Models\Project;
use App\Models\Stage;
use App\Models\StageProgress;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectGeneratorService
{
    public function __construct(private ActivityLogService $activityLog) {}

    public function generate(User $user, Template $template, string $title): Project
    {
        return DB::transaction(function () use ($user, $template, $title) {
            $stages = $template->projectStages();

            $project = Project::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'title' => $title,
                'status' => 'in_progress',
                'current_stage_id' => $stages->first()?->id,
            ]);

            $this->createProgressRecords($project, $stages);

            $this->activityLog->log(
                ActivityAction::ProjectCreated,
                $user,
                "Created project: {$project->title}"
            );

            return $project;
        });
    }

    private function createProgressRecords(Project $project, Collection $stages): void
    {
        $now = now();

        // أول مرحلة مفتوحة، والباقي مقفول
        $rows = $stages->values()->map(fn (Stage $stage, int $index) => [
            'project_id' => $project->id,
            'stage_id' => $stage->id,
            'status' => $index === 0 ? 'in_progress' : 'locked',
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        StageProgress::insert($rows);
    }
}
