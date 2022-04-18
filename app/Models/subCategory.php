<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id','subCategoryName'];

    public function category() {
        return $this->belongsTo(category::class);
    }

    public function products(){
        return $this->hasMany(product::class, 'subcategory_id','id');
    }
}
