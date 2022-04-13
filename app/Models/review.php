<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    use HasFactory;

    protected $fillable = ['stars','products_id','buyers_id', 'review'];

    public function product(){
        return $this->belongsTo(product::class);
    }
    public function buyers(){
        return $this->hasOne(buyer::class,'id', 'buyers_id')->select('id','name');

    }
}
