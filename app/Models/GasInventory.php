<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'gas_type_id',
        'quantity',
        'reorder_level',
    ];

    /**
     * Get the outlet that owns the inventory.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the gas type for this inventory item.
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
