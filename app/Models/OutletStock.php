<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'gas_type_id',
        'quantity',
        'available_quantity',
        'reserved_quantity',
        'minimum_stock_level',
        'last_restock_date',
        'status'
    ];

    protected $casts = [
        'last_restock_date' => 'datetime',
    ];

    /**
     * Get the outlet that owns this stock
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the gas type for this stock
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
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
     * Reserve stock for a customer request
     */
    public function reserve($quantity, $request_id)
    {
        if ($quantity > $this->available_quantity) {
            return false;
        }

        $this->available_quantity -= $quantity;
        $this->reserved_quantity += $quantity;
        $this->updateStatus();
        $this->save();

        return true;
    }

    /**
     * Release reserved stock
     */
    public function releaseReservation($quantity)
    {
        $this->reserved_quantity -= min($quantity, $this->reserved_quantity);
        $this->available_quantity += min($quantity, $this->reserved_quantity);
        $this->updateStatus();
        $this->save();

        return true;
    }

    /**
     * Confirm a reservation (reduce from stock)
     */
    public function confirmReservation($quantity)
    {
        $this->reserved_quantity -= min($quantity, $this->reserved_quantity);
        $this->quantity -= min($quantity, $this->quantity);
        $this->updateStatus();
        $this->save();

        return true;
    }

    /**
     * Get stock status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'normal':
                return '<span class="badge badge-success">Normal</span>';
            case 'low':
                return '<span class="badge badge-warning">Low</span>';
            case 'critical':
                return '<span class="badge badge-danger">Critical</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }
}
