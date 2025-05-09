<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total',
        'shipping_cost',
        'shipping_service_id',
        'shipping_service_name',
        'shipping_delivery_time',
        'shipping_tracking_code',
        'shipping_label_info',
        'shipping_date',
        'tax',
        'status',
        'payment_method',
        'payment_status',
        'transaction_code',
        'payment_details',
        'shipping_address_id',
        'billing_address_id',
        'notes',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Get the status history for the order.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Update the order status and record in history
     *
     * @param string $status New status
     * @param string|null $comment Optional comment about the status change
     * @param int|null $createdBy User ID who created this update (null = system)
     * @return bool
     */
    public function updateStatus(string $status, ?string $comment = null, ?int $createdBy = null): bool
    {
        $oldStatus = $this->status;
        
        // Only record if status is actually changing
        if ($status !== $oldStatus) {
            $this->status = $status;
            
            if ($this->save()) {
                // Record in history
                $this->statusHistory()->create([
                    'status' => $status,
                    'comment' => $comment ?? 'Status atualizado de ' . $oldStatus . ' para ' . $status,
                    'created_by' => $createdBy
                ]);
                
                return true;
            }
            
            return false;
        }
        
        return true; // No change needed
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Get the payment method for the order.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Retorna uma descrição formatada do status do pedido
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Em processamento',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$this->status] ?? 'Desconhecido';
    }

    /**
     * Retorna uma descrição formatada do status do pagamento
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Aguardando pagamento',
            'analyzing' => 'Em análise',
            'paid' => 'Pago',
            'available' => 'Disponível',
            'disputed' => 'Em disputa',
            'refunded' => 'Devolvido',
            'cancelled' => 'Cancelado',
            'debited' => 'Debitado',
            'withheld' => 'Retido temporariamente',
        ];

        return $labels[$this->payment_status] ?? 'Desconhecido';
    }

    /**
     * Retorna uma descrição formatada do método de pagamento
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'credit_card' => 'Cartão de Crédito',
            'boleto' => 'Boleto Bancário',
            'pix' => 'PIX',
        ];

        return $labels[$this->payment_method] ?? 'Desconhecido';
    }
}
