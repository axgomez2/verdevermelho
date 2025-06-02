<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\User;
use App\Models\VinylSec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Exibe a tela principal do PDV
     */
    public function index()
    {
        // Verificar se o usuário tem permissão de admin (role 66)
        if (Auth::user()->role != 66) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Você não tem permissão para acessar o PDV');
        }
        
        // Buscar os últimos 5 discos vendidos no PDV
        $recentSales = PosSale::with('items.vinylSec.vinyl.artists')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.pos.index', compact('recentSales'));
    }
    
    /**
     * Busca produtos pelo termo de pesquisa (AJAX)
     */
    public function search(Request $request)
    {
        // Log inicial da busca
        \Log::info("PDV - Request completo", [
            'all' => $request->all(),
            'content' => $request->getContent(),
            'headers' => $request->headers->all()
        ]);
        
        // Tentar obter o termo de várias formas possíveis
        $term = null;
        
        // Método 1: via input normal (form data)
        if ($request->has('term')) {
            $term = $request->input('term');
            \Log::info("PDV - Termo obtido via input", ['termo' => $term]);
        }
        // Método 2: via JSON
        else {
            $jsonData = $request->json()->all();
            if (isset($jsonData['term'])) {
                $term = $jsonData['term'];
                \Log::info("PDV - Termo obtido via JSON", ['termo' => $term]);
            }
        }
        
        \Log::info("PDV - Busca iniciada", [
            'termo' => $term,
            'ip' => $request->ip(),
            'user_id' => Auth::id()
        ]);
        
        if (empty($term)) {
            \Log::info("PDV - Termo vazio, retornando array vazio");
            return response()->json(['products' => []]);
        }
        
        // Buscar discos pelo título, artista, ou código de barras
        try {
            \Log::info("PDV - Executando query");
            
            $query = VinylSec::with(['vinylMaster.artists'])
                ->where('in_stock', 1)
                ->where('quantity', '>', 0)
                ->where(function($query) use ($term) {
                    // Busca pelo código de barras diretamente no VinylSec
                    $query->where('barcode', 'like', "%{$term}%");
                    
                    // Busca pelo título ou artista no VinylMaster relacionado
                    $query->orWhereHas('vinylMaster', function($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%")
                            ->orWhereHas('artists', function($artistQuery) use ($term) {
                                $artistQuery->where('name', 'like', "%{$term}%");
                            });
                    });
                })
                ->take(10);
            
            // Log da SQL gerada para diagnóstico
            \Log::info("PDV - SQL Gerada: " . $query->toSql(), ['bindings' => $query->getBindings()]);
            
            $results = $query->get();
            \Log::info("PDV - Resultados encontrados: " . $results->count());
            
            $products = $results->map(function($vinylSec) {
                try {
                    return [
                        'id' => $vinylSec->id,
                        'title' => $vinylSec->vinylMaster->title,
                        'artist' => $vinylSec->vinylMaster->artists->pluck('name')->implode(', '),
                        'price' => $vinylSec->price,
                        'stock' => $vinylSec->quantity,
                        'image' => $vinylSec->vinylMaster->cover_image ? asset('storage/' . $vinylSec->vinylMaster->cover_image) : asset('assets/images/placeholder.jpg'),
                        'barcode' => $vinylSec->barcode
                    ];
                } catch (\Exception $e) {
                    \Log::error("PDV - Erro ao processar disco: " . $e->getMessage(), [
                        'vinyl_sec_id' => $vinylSec->id,
                        'exception' => $e
                    ]);
                    return null;
                }
            })->filter();
            
            \Log::info("PDV - Resposta preparada", ['produtos_count' => $products->count()]);
            
            return response()->json(['products' => $products]);
            
        } catch (\Exception $e) {
            \Log::error("PDV - Erro na busca: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar discos: ' . $e->getMessage(),
                'products' => []
            ], 500);
        }
    }
    
    /**
     * Busca clientes pelo termo de pesquisa (AJAX)
     */
    public function searchCustomers(Request $request)
    {
        $term = $request->input('term');
        
        if (empty($term)) {
            return response()->json(['customers' => []]);
        }
        
        // Buscar clientes pelo nome, email ou CPF
        $customers = User::where('role', 20) // Apenas clientes comuns
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('cpf', 'like', "%{$term}%");
            })
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->cpf
                ];
            });
        
        return response()->json(['customers' => $customers]);
    }
    
    /**
     * Salva uma venda do PDV
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:vinyl_secs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'payment_method' => 'required|string|in:money,credit_card,debit_card,pix,transfer',
            'discount' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Obter os dados do vendedor (usuário logado com role 66)
            $seller = Auth::user();
            if ($seller->role != 66) {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas administradores podem registrar vendas no PDV'
                ], 403);
            }
            
            // Calcular valores totais
            $subtotal = 0;
            $discount = $request->input('discount', 0);
            $shipping = $request->input('shipping', 0);
            
            // Validar estoque dos itens
            $itemsData = [];
            foreach ($request->items as $item) {
                $vinylSec = VinylSec::find($item['id']);
                
                if (!$vinylSec || $vinylSec->in_stock != 1 || $vinylSec->quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Um ou mais produtos não possuem estoque suficiente'
                    ], 400);
                }
                
                $price = $vinylSec->price;
                $itemTotal = $price * $item['quantity'];
                $subtotal += $itemTotal;
                
                $itemsData[] = [
                    'vinyl_sec_id' => $vinylSec->id,
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'item_discount' => 0, // Desconto por item não implementado ainda
                    'item_total' => $itemTotal
                ];
                
                // Atualizar estoque
                $vinylSec->quantity -= $item['quantity'];
                if ($vinylSec->quantity <= 0) {
                    $vinylSec->in_stock = 0;
                }
                $vinylSec->save();
            }
            
            // Calcular total
            $total = $subtotal - $discount + $shipping;
            
            // Criar a venda
            $sale = PosSale::create([
                'user_id' => $request->input('user_id'),
                'customer_name' => $request->input('customer_name'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $request->input('payment_method'),
                'notes' => $request->input('notes'),
                'invoice_number' => PosSale::generateInvoiceNumber(),
                'seller_id' => $seller->id,
                'seller_name' => $seller->name
            ]);
            
            // Criar os itens da venda
            foreach ($itemsData as $itemData) {
                $sale->items()->create($itemData);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venda realizada com sucesso',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exibe os detalhes de uma venda
     */
    public function show($id)
    {
        $sale = PosSale::with(['items.vinylSec.vinyl.artists', 'customer', 'seller'])
            ->findOrFail($id);
        
        return view('admin.pos.show', compact('sale'));
    }
    
    /**
     * Lista todas as vendas realizadas no PDV
     */
    public function sales()
    {
        $sales = PosSale::with(['items', 'customer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.pos.sales', compact('sales'));
    }
}
