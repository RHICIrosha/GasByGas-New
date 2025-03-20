<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletOrderRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_order_request_id',
        'gas_type_id',
        'quantity_requested',
        'quantity_approved',
    ];

    /**
     * Get the request that owns the item.
     */
    public function request()
    {
        return $this->belongsTo(OutletOrderRequest::class, 'outlet_order_request_id');
    }

    /**
     * Get the gas type for this item.
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Calculate the approval percentage.
     */
    public function getApprovalPercentageAttribute()
    {
        if ($this->quantity_requested === 0 || is_null($this->quantity_approved)) {
            return 0;
        }

        return ($this->quantity_approved / $this->quantity_requested) * 100;
    }

    /**
     * Check if item is fully approved.
     */
    public function isFullyApproved()
    {
        return !is_null($this->quantity_approved) &&
               $this->quantity_approved === $this->quantity_requested;
    }

    /**
     * Check if item is partially approved.
     */
    public function isPartiallyApproved()
    {
        return !is_null($this->quantity_approved) &&
               $this->quantity_approved > 0 &&
               $this->quantity_approved < $this->quantity_requested;
    }

    /**
     * Check if item is rejected.
     */
    public function isRejected()
    {
        return !is_null($this->quantity_approved) && $this->quantity_approved === 0;
    }
}
