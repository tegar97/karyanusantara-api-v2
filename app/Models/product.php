<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function umkm(){
        return  $this->hasOne(umkm::class, 'id', 'umkm_id');
    }
    public function images(){
        return  $this->hasMany(productGallery::class, 'product_id', 'id');
    }

    public function category(){
        return $this->hasOne(category::class,'id','category_id');
    }

    
}