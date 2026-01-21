<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InspectionReportImage Model
 * 
 * Stores images/logs attached to inspection reports
 */
class InspectionReportImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_report_id',
        'image_url',
        'image_type',
        'caption',
        'sort_order',
    ];

    public function inspectionReport()
    {
        return $this->belongsTo(InspectionReport::class);
    }
}
