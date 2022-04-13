<?php

namespace App\Models;

use App\Http\Controllers\umkmController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itemCart extends Model
{
    use HasFactory;
    protected $table = 'itemCarts';

    protected $fillable =['products_id','quantity','isSelected', 'carts_id','umkm_id','service_courier','courier_price'];

    public function product(){
        return $this->hasOne(product::class,'id', 'products_id');
    }
    public function umkm()
    {
        return $this->belongsTo(umkm::class, 'umkm_id', 'id');
    }

    public function cart(){
        return $this->hasOne(cart::class,'id','carts_id');
    }
}
