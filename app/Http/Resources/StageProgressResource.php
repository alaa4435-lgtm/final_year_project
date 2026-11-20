<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stage_id' => $this->stage_id,
            'title' => $this->stage?->title,
            'order' => $this->stage?->order,
            'status' => $this->status,
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
