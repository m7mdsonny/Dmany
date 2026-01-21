<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WarrantyClaimImage Model
 * 
 * Images attached to warranty claims
 */
class WarrantyClaimImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_claim_id',
        'image_url',
        'description',
        'sort_order',
    ];

    public function warrantyClaim()
    {
        return $this->belongsTo(WarrantyClaim::class);
    }
}
