<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WarrantyClaim Model
 * 
 * Warranty claims made by buyers during warranty period
 */
class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_order_id',
        'user_id',
        'claim_number',
        'description',
        'status',
        'admin_response',
        'resolved_by',
        'resolved_at',
        'decision_outcome',
        'refund_amount',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function inspectionOrder()
    {
        return $this->belongsTo(InspectionOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function images()
    {
        return $this->hasMany(WarrantyClaimImage::class)->orderBy('sort_order');
    }

    /**
     * Generate unique claim number
     */
    public static function generateClaimNumber(): string
    {
        $year = date('Y');
        $lastClaim = static::where('claim_number', 'like', "WC-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastClaim) {
            $number = (int) substr($lastClaim->claim_number, -3) + 1;
        } else {
            $number = 1;
        }

        return sprintf('WC-%s-%03d', $year, $number);
    }
}
