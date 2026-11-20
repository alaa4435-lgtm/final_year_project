<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'current_stage_id' => $this->current_stage_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'template' => new TemplateResource($this->whenLoaded('template')),
            'stages' => StageProgressResource::collection($this->whenLoaded('stageProgress')),
        ];
    }
}
