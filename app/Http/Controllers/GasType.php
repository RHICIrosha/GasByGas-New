<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'price',
        'weight',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the head office stock for this gas type
     */
    public function headOfficeStock()
    {
        return $this->hasOne(HeadOfficeStock::class);
    }

    /**
     * Get all outlets stocking this gas type
     */
    public function outletStocks()
    {
        return $this->hasMany(OutletStock::class);
    }

    /**
     * Scope a query to only include active gas types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get stock history for this gas type
     */
    public function stockHistory()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rs. ' . number_format($this->price, 2);
    }

    /**
     * Get formatted weight with unit
     */
    public function getFormattedWeightAttribute()
    {
        return $this->weight . ' kg';
    }
}
