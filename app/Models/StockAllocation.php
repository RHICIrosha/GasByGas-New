<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAllocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'head_office_stock_id',
        'gas_type_id',
        'total_quantity',
        'allocated_quantity',
        'allocation_date',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'allocation_date' => 'datetime',
        'total_quantity' => 'decimal:2',
        'allocated_quantity' => 'decimal:2'
    ];

    /**
     * Relationship with HeadOfficeStock
     */
    public function headOfficeStock()
    {
        return $this->belongsTo(HeadOfficeStock::class);
    }

    /**
     * Relationship with GasType
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Create a head office reservation allocation
     */
    public static function createHeadOfficeReservation(HeadOfficeStock $stock, float $reserveQuantity)
    {
        return self::create([
            'head_office_stock_id' => $stock->id,
            'gas_type_id' => $stock->gas_type_id,
            'total_quantity' => $reserveQuantity,
            'allocated_quantity' => $reserveQuantity,
            'allocation_date' => now(),
            'status' => 'reserved',
            'notes' => 'Head office reservation'
        ]);
    }
}
