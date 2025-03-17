<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'gas_type_id',
        'quantity',
        'transaction_type', // restock, allocation, allocation_delivered, allocation_cancelled, return, adjustment
        'batch_number',
        'reference_id',
        'performed_by',
        'notes'
    ];

    /**
     * Get the gas type that this history belongs to
     */
    public function gasType()
    {
        return $this->belongsTo(GasType::class);
    }

    /**
     * Get the user who performed this action
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($history) {
            if (auth()->check()) {
                $history->performed_by = auth()->id();
            }
        });
    }
}
