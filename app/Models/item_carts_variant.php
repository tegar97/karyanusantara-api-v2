<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item_carts_variant extends Model
{
    use HasFactory;
    protected $table = 'item_carts_variant';
    protected $fillable = ['itemCarts_id', 'product_variantion_id', 'variants_option_id'];


    public function itemCarts(){
        return $this->hasOne(itemCart::class, 'itemCarts_id','id');
    }
}
