<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadOfficeStock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gas_type_id',
        'total_quantity',
        'available_quantity',
        'allocated_quantity',
        'minimum_stock_level',
        'last_restock_date',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_restock_date' => 'datetime',
        'total_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'allocated_quantity' => 'decimal:2',
        'minimum_stock_level' => 'decimal:2'
    ];

    /**
     * Calculate allocated quantity dynamically
     *
     * @return float
     */
    public function calculateAllocatedQuantity()
    {
        return max(0, $this->total_quantity - $this->available_quantity);
    }

    /**
     * Get the gas type that this stock belongs to
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Get the stock allocations for this stock
     */
    public function allocations()
    {
        return $this->hasMany(StockAllocation::class, 'head_office_stock_id');
    }

    /**
     * Update stock status based on current quantities
     */
    public function updateStatus()
    {
        $availableQuantity = $this->available_quantity;

        $this->status = match(true) {
            $availableQuantity <= $this->minimum_stock_level / 2 => 'critical',
            $availableQuantity <= $this->minimum_stock_level => 'low',
            default => 'normal'
        };
    }

    /**
     * Setter for allocated quantity
     *
     * @param float $value
     */
    public function setAllocatedQuantityAttribute($value)
    {
        $this->attributes['allocated_quantity'] = $this->calculateAllocatedQuantity();
    }

    /**
     * Getter for allocated quantity
     *
     * @return float
     */
    public function getAllocatedQuantityAttribute()
    {
        return $this->calculateAllocatedQuantity();
    }

    /**
     * Check if the stock is low
     *
     * @return bool
     */
    public function isLow()
    {
        return $this->available_quantity <= $this->minimum_stock_level;
    }

    /**
     * Check if the stock is critically low
     *
     * @return bool
     */
    public function isCritical()
    {
        return $this->available_quantity <= ($this->minimum_stock_level / 2);
    }
    public function calculateHeadOfficeAllocation()
    {
        // Simply return the difference between total and available quantity
        return $this->total_quantity - $this->available_quantity;
    }
}
