<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'delivery_number',
        'scheduled_date',
        'actual_date',
        'status',
        'notes',
        'total_quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_date' => 'datetime',
    ];

    /**
     * Get the outlet for this delivery.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Get the delivery items for this delivery.
     */
    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    /**
     * Generate a unique delivery number.
     */
    public function generateDeliveryNumber()
    {
        $this->delivery_number = 'DEL' . now()->format('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}
