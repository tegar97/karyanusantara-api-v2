<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buyerAddress extends Model
{
    use HasFactory;
    protected $table = 'buyer_address';

    protected $guarded = ['id'];

}
