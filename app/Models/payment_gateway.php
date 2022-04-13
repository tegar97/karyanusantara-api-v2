<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_gateway extends Model
{
    use HasFactory;
    protected $table = 'payment_gateway';

    protected $fillable = ['gateway_name', 'gateway_code', 'gateway_how_to_pay', 'gateway_logo', 'gateway_description'];
    
}
