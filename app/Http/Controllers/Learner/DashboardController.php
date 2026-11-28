<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = $user->projects()
            ->with('template')
            ->withCount([
                'stageProgress',
                'stageProgress as completed_stages_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->latest()
            ->get();

        $stats = [
            'total' => $projects->count(),
            'in_progress' => $projects->where('status', 'in_progress')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
        ];

        $current = $projects->firstWhere('status', 'in_progress');
        $recent = $projects->take(5);

        return view('learner.dashboard', compact('user', 'stats', 'current', 'recent'));
    }
}
