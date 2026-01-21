<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InspectionReport Model
 * 
 * Detailed inspection report for each order
 * Contains checklist results, scores, and final decision
 * Editable by admin/technician
 */
class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_order_id',
        'battery_health',
        'screen_condition',
        'camera_condition',
        'speaker_status',
        'network_status',
        'condition_score',
        'grade',
        'technician_notes',
        'checklist_results',
        'final_decision',
        'decision_date',
        'decision_by',
        'report_url',
        'decision_notes',
    ];

    protected $casts = [
        'battery_health' => 'integer',
        'condition_score' => 'integer',
        'checklist_results' => 'array',
        'decision_date' => 'datetime',
    ];

    // Relationships
    public function inspectionOrder()
    {
        return $this->belongsTo(InspectionOrder::class);
    }

    public function decisionBy()
    {
        return $this->belongsTo(User::class, 'decision_by');
    }

    public function images()
    {
        return $this->hasMany(InspectionReportImage::class)->orderBy('sort_order');
    }

    // Helper methods
    public function isPassed(): bool
    {
        return $this->final_decision === 'pass';
    }

    public function isFailed(): bool
    {
        return $this->final_decision === 'fail';
    }

    /**
     * Get overall condition assessment text
     */
    public function getConditionText(): string
    {
        if ($this->condition_score >= 9) return 'Excellent';
        if ($this->condition_score >= 7) return 'Very Good';
        if ($this->condition_score >= 5) return 'Good';
        if ($this->condition_score >= 3) return 'Fair';
        return 'Poor';
    }
}
