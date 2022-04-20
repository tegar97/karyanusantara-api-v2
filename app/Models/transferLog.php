<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transferLog extends Model
{
    use HasFactory;
    protected $table = "transfer_logs";
    protected $fillable = ['umkm_id','total','cost_reduction', 'transaction_id','status'];
}
