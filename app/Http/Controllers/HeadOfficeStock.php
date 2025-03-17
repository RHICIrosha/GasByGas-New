<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadOfficeStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'gas_type_id',
        'total_quantity',
        'available_quantity',
        'allocated_quantity',
        'minimum_stock_level',
        'last_restock_date',
        'next_expected_delivery',
        'batch_number',
        'status'
    ];

    protected $casts = [
        'last_restock_date' => 'datetime',
        'next_expected_delivery' => 'datetime',
    ];

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
        return $this->hasMany(StockAllocation::class);
    }

    /**
     * Check if the stock is low
     */
    public function isLow()
    {
        return $this->available_quantity <= $this->minimum_stock_level;
    }

    /**
     * Check if the stock is critically low
     */
    public function isCritical()
    {
        return $this->available_quantity <= ($this->minimum_stock_level / 2);
    }

    /**
     * Update stock status based on current quantities
     */
    public function updateStatus()
    {
        if ($this->isCritical()) {
            $this->status = 'critical';
        } elseif ($this->isLow()) {
            $this->status = 'low';
        } else {
            $this->status = 'normal';
        }

        $this->save();
    }

    /**
     * Allocate stock quantity
     */
    public function allocate($quantity, $outlet_id)
    {
        if ($quantity > $this->available_quantity) {
            return false;
        }

        $this->available_quantity -= $quantity;
        $this->allocated_quantity += $quantity;
        $this->updateStatus();
        $this->save();

        // Create allocation record
        StockAllocation::create([
            'head_office_stock_id' => $this->id,
            'outlet_id' => $outlet_id,
            'quantity' => $quantity,
            'status' => 'pending'
        ]);

        return true;
    }

    /**
     * Restock inventory
     */
    public function restock($quantity, $batch_number = null)
    {
        $this->total_quantity += $quantity;
        $this->available_quantity += $quantity;
        $this->last_restock_date = now();
        $this->batch_number = $batch_number ?? $this->batch_number;
        $this->updateStatus();
        $this->save();

        // Log stock history
        StockHistory::create([
            'gas_type_id' => $this->gas_type_id,
            'quantity' => $quantity,
            'transaction_type' => 'restock',
            'batch_number' => $batch_number,
            'notes' => 'Head office restock'
        ]);

        return true;
    }
}
