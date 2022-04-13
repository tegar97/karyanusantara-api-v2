<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courier extends Model
{
    protected $table = 'couriers';

    use HasFactory;
    protected $fillable = ['code','name','image'];

    public function sub_service_courier(){

    }
    
}
