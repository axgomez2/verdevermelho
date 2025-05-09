<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $melhorEnvioService;

    public function __construct(MelhorEnvioService $melhorEnvioService)
    {
        $this->melhorEnvioService = $melhorEnvioService;
    }

    /**
     * Exibe a listagem de pedidos
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = Order::with(['user', 'items.product']);
        
        // Filtro por status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        // Filtro por busca (reference, nome do cliente ou email)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtro por data
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Ordenação padrão: mais recentes primeiro
        $query->orderBy('created_at', 'desc');
        
        $orders = $query->paginate(20);
        
        // Estatísticas
        $statistics = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'canceled' => Order::where('status', 'canceled')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'statistics', 'status', 'search', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Exibe os detalhes de um pedido específico
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with([
            'items.product', 
            'user', 
            'shippingAddress', 
            'statusHistory' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Atualiza o status de um pedido
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,canceled,refunded',
            'description' => 'required|string|max:255',
            'notify_customer' => 'boolean'
        ]);
        
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->input('status');
        $notifyCustomer = $request->input('notify_customer', false);
        
        // Atualiza o status do pedido
        $order->status = $newStatus;
        
        // Processa etiqueta de envio se o status for alterado para "em transporte"
        if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
            $this->processShippingLabel($order);
        }
        
        $order->save();
        
        // Adiciona ao histórico
        $order->statusHistory()->create([
            'status' => $newStatus,
            'description' => $request->input('description'),
            'is_customer_notified' => $notifyCustomer
        ]);
        
        // Notifica o cliente se solicitado
        if ($notifyCustomer) {
            $this->notifyCustomer($order, $newStatus, $request->input('description'));
        }
        
        return redirect()->route('admin.orders.show', $order->id)
                       ->with('success', 'Status do pedido atualizado com sucesso.');
    }
    
    /**
     * Atualiza informações de rastreamento do pedido
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:50',
            'shipping_company' => 'required|string|max:100',
            'notify_customer' => 'boolean'
        ]);
        
        $order = Order::findOrFail($id);
        
        // Atualiza informações de rastreamento
        $order->tracking_code = $request->input('tracking_code');
        $order->shipping_company = $request->input('shipping_company');
        $order->save();
        
        // Adiciona ao histórico
        $description = "Informações de rastreamento atualizadas: {$request->input('shipping_company')} - {$request->input('tracking_code')}";
        $notifyCustomer = $request->input('notify_customer', false);
        
        $order->statusHistory()->create([
            'status' => $order->status,
            'description' => $description,
            'is_customer_notified' => $notifyCustomer
        ]);
        
        // Notifica o cliente se solicitado
        if ($notifyCustomer) {
            $this->notifyCustomer($order, $order->status, $description);
        }
        
        return redirect()->route('admin.orders.show', $order->id)
                       ->with('success', 'Informações de rastreamento atualizadas com sucesso.');
    }
    
    /**
     * Gera etiqueta de envio via Melhor Envio
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateShippingLabel($id)
    {
        $order = Order::with(['items.product', 'shippingAddress'])->findOrFail($id);
        
        try {
            // Verifica se o pedido já tem etiqueta
            if ($order->shipping_label_url) {
                return redirect()->route('admin.orders.show', $order->id)
                               ->with('info', 'Este pedido já possui uma etiqueta de envio.');
            }
            
            // Verifica se o pedido está pago
            if ($order->status !== 'paid' && $order->status !== 'processing') {
                return redirect()->route('admin.orders.show', $order->id)
                               ->with('error', 'Apenas pedidos pagos ou em processamento podem ter etiquetas geradas.');
            }
            
            // Gera a etiqueta via Melhor Envio
            $result = $this->melhorEnvioService->generateShippingLabel($order);
            
            if (!$result['success']) {
                throw new \Exception($result['message']);
            }
            
            // Atualiza o pedido com as informações da etiqueta
            $order->shipping_label_url = $result['label_url'];
            
            // Verificar se temos código de rastreio e salvar
            if (isset($result['tracking'])) {
                $order->tracking_code = $result['tracking'];
                // Define a transportadora com base no método de frete
                if (strpos(strtoupper($order->shipping_method), 'CORREIOS') !== false) {
                    $order->shipping_company = 'Correios';
                } elseif (strpos(strtoupper($order->shipping_method), 'JADLOG') !== false) {
                    $order->shipping_company = 'Jadlog';
                } else {
                    $order->shipping_company = 'Melhor Envio';
                }
            }
            
            // Atualiza o status para 'processing' se ainda estiver como 'paid'
            if ($order->status === 'paid') {
                $order->status = 'processing';
            }
            
            $order->save();
            
            // Adiciona ao histórico
            $order->statusHistory()->create([
                'status' => $order->status,
                'description' => "Etiqueta de envio gerada via Melhor Envio ({$result['shipping_company']})",
                'is_customer_notified' => false
            ]);
            
            return redirect()->route('admin.orders.show', $order->id)
                           ->with('success', 'Etiqueta de envio gerada com sucesso.');
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta: ' . $e->getMessage());
            
            return redirect()->route('admin.orders.show', $order->id)
                           ->with('error', 'Erro ao gerar etiqueta: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa a geração da etiqueta de envio
     *
     * @param Order $order
     * @return void
     */
    private function processShippingLabel(Order $order)
    {
        // Verifica se já existe uma etiqueta
        if ($order->shipping_label_url) {
            return;
        }
        
        try {
            // Apenas gera automaticamente se o método de envio usar Melhor Envio
            if (strpos($order->shipping_method, 'Correios') !== false || 
                strpos($order->shipping_method, 'Jadlog') !== false) {
                
                $order->load(['items.product', 'shippingAddress']);
                $result = $this->melhorEnvioService->generateShippingLabel($order);
                
                if ($result['success']) {
                    $order->shipping_label_url = $result['label_url'];
                    
                    // Verificar se temos código de rastreio e salvar
                    if (isset($result['tracking'])) {
                        $order->tracking_code = $result['tracking'];
                        // Define a transportadora com base no método de frete
                        if (strpos(strtoupper($order->shipping_method), 'CORREIOS') !== false) {
                            $order->shipping_company = 'Correios';
                        } elseif (strpos(strtoupper($order->shipping_method), 'JADLOG') !== false) {
                            $order->shipping_company = 'Jadlog';
                        } else {
                            $order->shipping_company = 'Melhor Envio';
                        }
                    }
                    
                    $order->save();
                    
                    Log::info('Etiqueta gerada automaticamente para o pedido #' . $order->reference);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta automaticamente: ' . $e->getMessage());
        }
    }
    
    /**
     * Notifica o cliente sobre atualização do pedido
     *
     * @param Order $order
     * @param string $status
     * @param string $description
     * @return void
     */
    private function notifyCustomer(Order $order, $status, $description)
    {
        // Job para enviar notificação por e-mail
        dispatch(new \App\Jobs\SendOrderStatusUpdateEmail($order, $status, $description));
    }
}
