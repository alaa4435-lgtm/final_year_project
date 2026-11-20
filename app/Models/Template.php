<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'thumbnail', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * المراحل اللي يمر بها المشروع بالترتيب:
     * Stage 0 العامة + مراحل القالب نفسه.
     */
    public function projectStages(): Collection
    {
        return Stage::query()
            ->where('is_global', true)
            ->orWhere('template_id', $this->id)
            ->orderBy('order')
            ->get();
    }
}
