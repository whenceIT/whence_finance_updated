<?php

namespace App\Services;


use Illuminate\Database\Eloquent\Model;

class AuditorService
{
    /**
     * Get all audits with optional filters
     */
    public function getAudits(array $filters = [])
    {
        $query = \OwenIt\Auditing\Models\Audit::query();

        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (!empty($filters['auditable_id'])) {
            $query->where('auditable_id', $filters['auditable_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['created_at_from'])) {
            $query->where('created_at', '>=', $filters['created_at_from']);
        }

        if (!empty($filters['created_at_to'])) {
            $query->where('created_at', '<=', $filters['created_at_to']);
        }

        if (!empty($filters['time_period'])) {
            if ($filters['time_period'] === 'work_hours') {
                $query->whereRaw("TIME(created_at) BETWEEN '06:00' AND '17:00'");
            } elseif ($filters['time_period'] === 'after_hours') {
                $query->where(function($q) {
                    $q->whereRaw("TIME(created_at) BETWEEN '19:00' AND '23:59'")
                      ->orWhereRaw("TIME(created_at) BETWEEN '00:00' AND '05:00'");
                });
            }
        }

        if (!empty($filters['user_name'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['user_name']}%")
                  ->orWhere('last_name', 'like', "%{$filters['user_name']}%");
            });
        }

        return $query->with('user.roles', 'auditable')->orderBy('created_at', 'desc')->paginate(50);
    }

    /**
     * Get audits for a specific model
     */
    public function getAuditsForModel(Model $model)
    {
        return $model->audits()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get audit by ID
     */
    public function getAuditById($id)
    {
        return \OwenIt\Auditing\Models\Audit::findOrFail($id);
    }

    /**
     * Delete an audit (soft delete or hard, depending on need)
     */
    public function deleteAudit($id)
    {
        $audit = \OwenIt\Auditing\Models\Audit::findOrFail($id);
        return $audit->delete();
    }

    /**
     * Get audit events
     */
    public function getAuditEvents()
    {
        return \OwenIt\Auditing\Models\Audit::distinct('event')->pluck('event');
    }

    /**
     * Get auditable types
     */
    public function getAuditableTypes()
    {
        return \OwenIt\Auditing\Models\Audit::distinct('auditable_type')->pluck('auditable_type');
    }

    /**
     * Log a custom audit event
     */
    public function logCustomAudit($auditableType, $auditableId, $event, $userId, $request, $oldValues = [], $newValues = [], $tags = null)
    {
        \OwenIt\Auditing\Models\Audit::create([
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'event' => $event,
            'user_id' => $userId,
            'url' => $request->url(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'tags' => $tags
        ]);
    }

    /**
     * Log login event
     */
    public function logLogin($userId, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $userId,
            'logged in',
            $userId,
            $request,
            [],
            ['action' => 'logged in'],
            'authentication'
        );
    }
}