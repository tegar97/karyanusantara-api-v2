<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transction_item extends Model
{
    use HasFactory;
    protected $fillable = ['transaction_id', 'quantity', 'product_id','amount'];

    public function product(){
        return $this->hasOne(product::class,'id','product_id');
    }
}
