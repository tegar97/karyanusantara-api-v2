<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_service_courier_setting extends Model
{
    protected $table = 'sub_service_courier_setting';

    use HasFactory;
    protected $fillable = ['courier_setting_id', 'sub_services_courier_id', 'status'];
}
