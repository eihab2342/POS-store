<?php

// فاتورة شراء من مورد
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['supplier_id','date', 'total_cost', 'invoice_no'];
    public function supplier(){ return $this->belongsTo(Supplier::class); }
    public function items(){ return $this->hasMany(PurchaseItem::class); }
}