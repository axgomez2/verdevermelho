<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSale extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'customer_name',
        'subtotal',
        'discount',
        'shipping',
        'total',
        'payment_method',
        'notes',
        'invoice_number',
        'seller_id',
        'seller_name'
    ];
    
    /**
     * Gera um número único para a nota
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'PDV';
        $date = now()->format('Ymd');
        $lastSale = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastSale ? (int)substr($lastSale->invoice_number, -4) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Relacionamento com os itens da venda
     */
    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class, 'pos_sale_id');
    }
    
    /**
     * Relacionamento com o cliente (se existir)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relacionamento com o vendedor
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
