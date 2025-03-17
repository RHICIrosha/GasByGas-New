<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'district',
        'phone',
        'email',
        'manager_id',
        'is_active',
        'open_time',
        'close_time',
        'capacity',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who manages this outlet
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all stocks for this outlet
     */
    public function stocks()
    {
        return $this->hasMany(OutletStock::class);
    }

    /**
     * Get all stock allocations for this outlet
     */
    public function stockAllocations()
    {
        return $this->hasMany(StockAllocation::class);
    }

    /**
     * Get all customer requests for this outlet
     */
    public function customerRequests()
    {
        return $this->hasMany(CustomerRequest::class);
    }

    /**
     * Scope a query to only include active outlets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get active status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';
    }
}
