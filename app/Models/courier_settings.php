<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courier_settings extends Model
{
    protected $table = 'courier_settings';

    use HasFactory;
    protected $fillable = ['umkm_id', 'courier_id', 'status'];

    public function courier(){
        return $this->hasOne(courier::class, 'id', 'courier_id');
    }
}
