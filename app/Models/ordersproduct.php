<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordersproduct extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $fillable = ['orders_id', 'quantity', 'product_id', 'amount'];


    public function umkm()
    {
        return $this->belongsTo(umkm::class, 'umkm_id', 'id');
    }

    public function product(){
        return $this->hasOne(product::class, 'id','product_id');
    }


}
