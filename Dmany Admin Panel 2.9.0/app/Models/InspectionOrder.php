<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * InspectionOrder Model
 * 
 * Core transaction table for Inspection & Warranty orders
 * Links product, buyer, seller, and inspection report
 */
class InspectionOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'item_id',
        'buyer_id',
        'seller_id',
        'device_price',
        'inspection_fee',
        'total_amount',
        'status',
        'assigned_technician_id',
        'device_received_at',
        'inspection_date',
        'delivery_date',
        'warranty_start_date',
        'warranty_end_date',
        'warranty_duration',
        'internal_notes',
        'admin_notes',
    ];

    protected $casts = [
        'device_price' => 'decimal:2',
        'inspection_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'device_received_at' => 'datetime',
        'inspection_date' => 'datetime',
        'delivery_date' => 'datetime',
        'warranty_start_date' => 'date',
        'warranty_end_date' => 'date',
        'warranty_duration' => 'integer',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function inspectionReport()
    {
        return $this->hasOne(InspectionReport::class);
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(InspectionAuditLog::class);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderInspection(): bool
    {
        return $this->status === 'under_inspection';
    }

    public function isPassed(): bool
    {
        return $this->status === 'passed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isWarrantyActive(): bool
    {
        return $this->status === 'warranty_active';
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = static::where('order_number', 'like', "IW-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $number = (int) substr($lastOrder->order_number, -3) + 1;
        } else {
            $number = 1;
        }

        return sprintf('IW-%s-%03d', $year, $number);
    }

    /**
     * Calculate warranty end date from start date
     */
    public function calculateWarrantyEndDate(): \DateTime
    {
        $startDate = $this->warranty_start_date ?? new \DateTime();
        $endDate = clone $startDate;
        $endDate->modify("+{$this->warranty_duration} days");
        return $endDate;
    }

    /**
     * Get remaining warranty days
     */
    public function getRemainingWarrantyDays(): ?int
    {
        if (!$this->warranty_start_date || !$this->warranty_end_date) {
            return null;
        }

        $now = new \DateTime();
        $end = new \DateTime($this->warranty_end_date);
        
        if ($now > $end) {
            return 0;
        }

        return $now->diff($end)->days;
    }
}
