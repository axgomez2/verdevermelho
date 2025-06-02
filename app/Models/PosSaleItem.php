<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSaleItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pos_sale_id',
        'vinyl_sec_id',
        'price',
        'quantity',
        'item_discount',
        'item_total'
    ];
    
    /**
     * Relacionamento com a venda
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }
    
    /**
     * Relacionamento com o disco (VinylSec)
     */
    public function vinylSec(): BelongsTo
    {
        return $this->belongsTo(VinylSec::class, 'vinyl_sec_id');
    }
    
    /**
     * Acessor para o disco completo com suas informações
     */
    public function getVinylAttribute()
    {
        return $this->vinylSec->vinyl ?? null;
    }
}
