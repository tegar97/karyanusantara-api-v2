<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productVariants extends Model
{

    protected  $table = 'product_variant';
    protected $fillable = ['product_id','variantName','type'];
    use HasFactory;

    public function variantOption(){
        return $this->hasMany(variantsOption::class, 'product_variantion_id','id');

    }
}
