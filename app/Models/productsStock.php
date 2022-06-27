<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productsStock extends Model
{
    protected $table = 'products_stock';
    protected $fillable = ['totalStock','unitPrice'];
    use HasFactory;
}
