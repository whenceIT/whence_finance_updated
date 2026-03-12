<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RecoveryDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'recovery_case_id','uploaded_by','document_type',
        'title','file_path','file_name','mime_type','file_size','notes',
    ];

    public function recoveryCase(): BelongsTo { return $this->belongsTo(RecoveryCase::class); }
    public function uploadedBy(): BelongsTo   { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getUrlAttribute(): string
    {
        return url('recovery/case/' . $this->recovery_case_id . '/document/' . $this->id . '/download');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
