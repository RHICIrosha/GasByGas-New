<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'gas_type_id',
        'outlet_id',
        'quantity',  // Add this
        'status',
        'notes',
        'empty_cylinder_received',
        'payment_received',
        'amount',
        'expected_pickup_date',
        'actual_pickup_date'
    ];

    protected $casts = [
        'empty_cylinder_returned' => 'boolean',
        'payment_received' => 'boolean',
        'quantity' => 'integer',
        'amount' => 'decimal:2',
        'expected_pickup_date' => 'date',
        'actual_pickup_date' => 'date',
    ];
    public $sortable = [
        'id',
        'request_number',
        'quantity',
        'status',
        'amount',
        'expected_pickup_date',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function token()
    {
        return $this->hasOne(Token::class);
    }

    public function generateRequestNumber()
    {
        // Format: GR-YYMMDD-XXXX (GR = Gas Request, YYMMDD = Date, XXXX = Random)
        $prefix = 'GR-' . date('ymd') . '-';
        $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
        $this->request_number = $prefix . $random;

        return $this;
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function scopeFromOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }
    public function scopeReadyForPickup($query)
    {
        return $query->where('status', 'Ready for Pickup')
                    ->where('expected_pickup_date', '<=', now()->addDays(14));
    }
    public function scopeExpired($query)
    {
        return $query->where('status', 'Ready for Pickup')
                    ->where('expected_pickup_date', '<', now()->subDays(14));
    }

    /**
     * Get total amount of the request.
     */
    public function getTotalAttribute()
    {
        return $this->amount * $this->quantity;
    }

    /**
     * Check if the request is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'Completed';
    }

    /**
     * Check if the request can be picked up.
     */
    public function canBePickedUp()
    {
        return $this->status === 'Ready for Pickup' &&
               $this->expected_pickup_date !== null &&
               $this->expected_pickup_date->lte(now()->addDays(14));
    }

    /**
     * Check if the request is expired.
     */
    public function isExpired()
    {
        return $this->status === 'Ready for Pickup' &&
               $this->expected_pickup_date !== null &&
               $this->expected_pickup_date->lt(now()->subDays(14));
    }

}
