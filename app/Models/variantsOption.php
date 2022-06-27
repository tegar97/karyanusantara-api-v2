<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class variantsOption extends Model
{

    protected $table = 'variants_option';
    protected $fillable = ['product_variantion_id','variantName', 'variantionImg','sku','price','product_stock_id'];
    use HasFactory;
}
