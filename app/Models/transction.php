<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transction extends Model
{
    use HasFactory;
    protected $table = 'transaction';
    protected $fillable = ['invoice', 'amount', 'shipping_amount', 'logistic_code', 'logistic_type', 'transction_item_id', 'umkm_id', 'buyers_id', 'payment_id', 'resi', 'status', 'status_str', 'buyers_complate_address'];

    public function transactionItem(){
        return $this->hasMany(transction_item::class,'transaction_id','id');
    }

    public function umkm(){
        return $this->hasOne(umkm::class,'id','umkm_id');
    }
    public function buyers(){
        return $this->hasOne(buyer::class,'id','buyers_id');
    }

    public function transferLog(){
        return $this->hasOne(transferLog::class, 'transaction_id', 'id');

    }
}
