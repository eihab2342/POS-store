<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'points'];

    public function sale()
    {
        return $this->hasMany(Sale::class);
    }
}