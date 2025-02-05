<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatStyleShop extends Model
{
    use HasFactory;

    protected $table = 'cat_style_shop';
    protected $fillable = ['nome', 'slug'];

    public function vinylSecs()
    {
        return $this->hasMany(VinylSec::class);
    }
}
