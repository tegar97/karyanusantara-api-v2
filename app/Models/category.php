<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;

    protected $fillable = ['categoryName','categoryIcon'];


    public function subCategory() {
        return $this->hasMany(subCategory::class,'category_id','id');
    }

    public function product() {
        return $this->hasMany(product::class,'category_id','id');
    }

}
