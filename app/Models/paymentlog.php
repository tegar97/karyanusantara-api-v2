<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentlog extends Model
{
    use HasFactory;
    protected $table = 'paymentlog';
    protected $fillable = ['payment_status_int', 'payment_status_str', 'raw_response', 'payments_id', 'payment_type'];
}
