<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InspectionConfiguration Model
 * 
 * Global settings for Inspection & Warranty service
 * Only one configuration record should exist (singleton pattern)
 * Changes here affect all new orders immediately
 */
class InspectionConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_percentage',
        'warranty_duration',
        'service_description',
        'workflow_steps',
        'terms_conditions',
        'covered_items',
        'excluded_items',
        'is_active',
    ];

    protected $casts = [
        'fee_percentage' => 'decimal:2',
        'warranty_duration' => 'integer',
        'covered_items' => 'array',
        'excluded_items' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the single configuration instance (singleton pattern)
     * Creates default if none exists
     */
    public static function getConfiguration(): self
    {
        return static::firstOrCreate(
            [],
            [
                'fee_percentage' => 4.00,
                'warranty_duration' => 5,
                'is_active' => true,
            ]
        );
    }

    /**
     * Calculate inspection fee from device price
     */
    public function calculateInspectionFee(float $devicePrice): float
    {
        return ($devicePrice * $this->fee_percentage) / 100;
    }

    /**
     * Get total amount (device price + inspection fee)
     */
    public function calculateTotalAmount(float $devicePrice): float
    {
        return $devicePrice + $this->calculateInspectionFee($devicePrice);
    }
}
