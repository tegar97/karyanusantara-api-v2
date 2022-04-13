<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $fillable = ['buyers_id', 'itemCarts_id','total'];

    public function itemCart(){
        return $this->hasMany(itemCart::class, 'carts_id', 'id');
    }
}
