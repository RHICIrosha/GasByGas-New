<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletOrderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'request_number',
        'status',
        'notes',
        'requested_date',
        'delivery_date',
        'manager_id',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the outlet that owns the request.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the manager who created the request.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the admin who approved the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items for this request.
     */
    public function items()
    {
        return $this->hasMany(OutletOrderRequestItem::class);
    }

    /**
     * Generate a unique request number.
     */
    public function generateRequestNumber()
    {
        $prefix = 'OOR-' . date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        $this->request_number = $prefix . '-' . $random;

        return $this;
    }

    /**
     * Calculate the total requested quantity across all items.
     */
    public function getTotalRequestedQuantityAttribute()
    {
        return $this->items->sum('quantity_requested');
    }

    /**
     * Calculate the total approved quantity across all items.
     */
    public function getTotalApprovedQuantityAttribute()
    {
        return $this->items->sum('quantity_approved');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope a query to only include fulfilled requests.
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', 'Fulfilled');
    }
}
