<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ordersproduct extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'product_id','quantity','price'];


    

}
