<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageProgress extends Model
{
    protected $fillable = [
        'project_id', 'stage_id', 'html_code', 'css_code',
        'js_code', 'status', 'completed_at',
    ];

    protected $casts = ['completed_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }
}
