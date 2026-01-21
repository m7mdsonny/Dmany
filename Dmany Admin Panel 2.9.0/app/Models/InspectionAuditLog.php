<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InspectionAuditLog Model
 * 
 * Tracks all admin actions for audit trail
 * Ensures full accountability and transparency
 */
class InspectionAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_order_id',
        'user_id',
        'action_type',
        'action_description',
        'old_values',
        'new_values',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
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

    /**
     * Log an admin action
     */
    public static function logAction(
        int $orderId,
        int $userId,
        string $actionType,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $notes = null
    ): self {
        return static::create([
            'inspection_order_id' => $orderId,
            'user_id' => $userId,
            'action_type' => $actionType,
            'action_description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes,
        ]);
    }
}
