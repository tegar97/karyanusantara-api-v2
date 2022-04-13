<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    use HasFactory;
      protected $table = 'payments';

    protected $fillable = ['snap_url','buyers_id','amount','expire_time_unix', 'expire_time_str','paymet_gateway_id','payment_status','payment_code','payment_key', 'payment_url', 'midtrans_order_id'];

    public function paymentGateway(){
      return $this->hasOne(payment_gateway::class, 'id','paymet_gateway_id');
    }

    public function order(){
      return $this->hasMany(order::class, 'payments_id', 'id');
    }
}
