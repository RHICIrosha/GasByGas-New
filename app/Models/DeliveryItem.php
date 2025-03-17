<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'gas_type_id',
        'quantity',
    ];

    /**
     * Get the delivery that owns the item.
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the gas type for this delivery item.
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }
}
