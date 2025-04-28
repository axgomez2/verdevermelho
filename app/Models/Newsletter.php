<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'email',
        'is_active',
        'verified_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'verified_at' => 'datetime'
    ];
    
    /**
     * Verifica se o email já está cadastrado na newsletter
     *
     * @param string $email
     * @return bool
     */
    public static function isEmailRegistered(string $email): bool
    {
        return self::where('email', $email)->exists();
    }
}
