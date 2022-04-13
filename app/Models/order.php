<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'shipping_amount', 'logistic_code', 'logistic_type', 'payments_id', 'umkm_id'];

    public function umkm()
    {
        return  $this->hasOne(umkm::class, 'id', 'umkm_id');
    }

    public function orderItem()
    {
        return $this->hasMany(ordersproduct::class, 'orders_id', 'id');
    }
}
