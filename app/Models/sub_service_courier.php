<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_service_courier extends Model
{
    protected $table = 'sub_service_courier';

    use HasFactory;
    protected $fillable = ['service', 'description', 'courier_id'];

    public function sub_service_courier()
    {
    }
}
