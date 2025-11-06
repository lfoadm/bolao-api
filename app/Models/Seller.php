<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    /** @use HasFactory<\Database\Factories\SellerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'pix_key',
        'bio',
    ];

    /**
     * Um Seller pertence a um usuário (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Um Seller pode ter vários Pools (bolões criados).
     */
    public function pools()
    {
        return $this->hasMany(Pool::class);
    }
}
