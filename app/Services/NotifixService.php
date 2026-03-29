<?php

namespace App\Services;

use App\Models\Notifix;
use Illuminate\Support\Facades\DB;

class NotifixService
{
    /**
     * Create a new notification or update existing one for a user.
     * Each user has only one record, and the note object is updated with new notifications.
     *
     * @param int $userId
     * @param array $positions Array of position IDs
     * @param array $notificationData Notification details:
     *   - id: unique notification id
     *   - loan_id: loan identifier (optional)
     *   - from_id: sender user id
     *   - link_from: source link
     *   - link_to: destination link
     *   - type: notification type
     *   - message: notification message
     *   - positions: array of related positions
     *   - created_date: timestamp
     *
     * @return Notifix
     */
    public function create($userId, $positions, $notificationData)
    {
        return DB::transaction(function () use ($userId, $positions, $notificationData) {
            $existingRecord = $this->getMyNotifix($userId);

            if ($existingRecord) {
                // Update existing record - append new notification to the note array
                $existingNotes = $existingRecord->note ?? [];
                
                // Add new notification to the beginning of the array (newest first)
                array_unshift($existingNotes, array_merge($notificationData, [
                    'created_date' => now()->toIso8601String()
                ]));

                // Update positions if needed
                $existingPositions = $existingRecord->positions ?? [];
                $updatedPositions = array_unique(array_merge($existingPositions, $positions));

                $existingRecord->update([
                    'positions' => $updatedPositions,
                    'note' => $existingNotes
                ]);

                return $existingRecord->fresh();
            } else {
                // Create new record
                return Notifix::create([
                    'user_id' => $userId,
                    'positions' => $positions,
                    'note' => [array_merge($notificationData, [
                        'created_date' => now()->toIso8601String()
                    ])]
                ]);
            }
        });
    }

    /**
     * Delete a notifix record for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function delete($userId)
    {
        $record = $this->getMyNotifix($userId);
        
        if ($record) {
            return $record->delete();
        }
        
        return false;
    }

    /**
     * Get all notifix records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Notifix::all();
    }

    /**
     * Get notifix record for a specific user.
     *
     * @param int $userId
     * @return Notifix|null
     */
    public function getMyNotifix($userId)
    {
        return Notifix::where('user_id', $userId)->first();
    }

    /**
     * Get notifications for a specific position.
     *
     * @param int $positionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByPosition($positionId)
    {
        return Notifix::whereRaw('JSON_CONTAINS(positions, ?)', [json_encode($positionId)])->get();
    }

    /**
     * Remove a specific notification from user's notifix by notification id.
     *
     * @param int $userId
     * @param string $notificationId
     * @return bool
     */
    public function removeNotification($userId, $notificationId)
    {
        $record = $this->getMyNotifix($userId);
        
        if ($record && $record->note) {
            $notes = $record->note;
            $filteredNotes = array_filter($notes, function ($note) use ($notificationId) {
                return ($note['id'] ?? '') !== $notificationId;
            });

            $record->update(['note' => array_values($filteredNotes)]);
            return true;
        }
        
        return false;
    }

    /**
     * Clear all notifications for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function clearAll($userId)
    {
        $record = $this->getMyNotifix($userId);
        
        if ($record) {
            return $record->update(['note' => []]);
        }
        
        return false;
    }

    /**
     * Get unread notification count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        $record = $this->getMyNotifix($userId);
        return $record ? count($record->note ?? []) : 0;
    }
}