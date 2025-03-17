<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'gas_type_id',
        'previous_quantity',
        'new_quantity',
        'change_type',
        'notes',
    ];

    /**
     * Get the user who made the change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the outlet related to this inventory change.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the gas type for this inventory change.
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
