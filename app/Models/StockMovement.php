<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'qty',
        'type',
        'reason',
        'ref_type',
    ];


    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}