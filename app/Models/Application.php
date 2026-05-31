<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'reference_number',
    'doc_type',
    'applicant_ic',
    'applicant_name',
    'spouse_name',
    'spouse_ic',
    'applicant_address',
    'form_data',
    'status',
    'ai_score',
    'ai_eta',
    'sla_state',
    'assigned_officer_id',
    'notes',
])]
class Application extends Model
{
    use HasFactory;

    public const STAGES = ['received', 'verified', 'officer_review', 'approved', 'issued'];

    public const STAGE_LABELS = [
        'received' => 'Diterima',
        'verified' => 'Disahkan',
        'officer_review' => 'Semakan Pegawai',
        'approved' => 'Diluluskan',
        'issued' => 'Dikeluarkan',
        'rejected' => 'Ditolak',
    ];

    public const DOC_LABELS = [
        'birth' => 'Sijil Kelahiran',
        'marriage' => 'Sijil Perkahwinan',
        'mykad' => 'MyKAD',
    ];

    protected $casts = [
        'ai_score' => 'float',
        'ai_eta' => 'datetime',
        'form_data' => 'array',
    ];

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'applicant_ic', 'ic');
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderByDesc('created_at');
    }

    public function stageIndex(): int
    {
        return array_search($this->status, self::STAGES, true) ?: 0;
    }
}
