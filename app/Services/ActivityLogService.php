<?php

namespace App\Services;

use App\ActivityAction;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Str;

class ActivityLogService
{
    public function log(
        ActivityAction $action,
        ?User $user = null,
        ?string $description = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action->value,
            'description' => $description,
            'ip_address' => $this->resolveIp(),
            'user_agent' => $this->resolveUserAgent(),
        ]);
    }

    protected function resolveIp(): ?string
    {
        try {
            return RequestFacade::ip();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveUserAgent(): ?string
    {
        try {
            $agent = RequestFacade::userAgent();

            return $agent ? Str::limit($agent, 255, '') : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
