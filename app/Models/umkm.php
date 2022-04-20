<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Midtrans\Transaction;

class umkm extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory;
    protected $table = 'umkm';
    protected $guarded = ['id'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function getAuthPassword()
    {
        return $this->password;
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

   
    public function product(){
        return $this->hasMany(product::class,'umkm_id','id');
    }
    public function courier()
    {
        return $this->hasMany(courier_settings::class, 'umkm_id', 'id');
    }

    public function itemCart(){
        return $this->hasMany(itemCart::class,'umkm_id','id');
    }

    public function transaction(){
        return $this->hasMany(transction::class, 'umkm_id', 'id');

    }


    
}
