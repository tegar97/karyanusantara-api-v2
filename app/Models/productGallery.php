<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class productGallery extends Model
{
    protected $table = 'product_gallery';

    protected $fillable = [
        'imageName','url', 'product_id'
    ];

    public function getUrlAttribute($url)
    {
        return config('app.url') . Storage::url($url);
    }

    use HasFactory;
}
