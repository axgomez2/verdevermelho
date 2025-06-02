<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wantlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WantlistController extends Controller
{
    public function index()
    {
        $wantlistItems = Wantlist::where('user_id', Auth::id())
            ->with(['product'])
            ->get()
            ->map(function ($item) {
                return $item->product;
            });

        return view('site.wishlist.wantlist', compact('wantlistItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|string',
        ]);

        $wantlistItem = Wantlist::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
        ])->first();

        if ($wantlistItem) {
            $wantlistItem->delete();
            $message = 'Item removido da Wantlist';
            $inWantlist = false;
        } else {
            Wantlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'product_type' => $request->product_type,
            ]);
            $message = 'Item adicionado à Wantlist';
            $inWantlist = true;
        }
        
        // Obter a contagem atualizada de itens na wantlist
        $wantlist_count = Wantlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'wantlist_count' => $wantlist_count,
            'in_wantlist' => $inWantlist,
        ]);
    }
    
    public function remove(Request $request, $id)
    {
        // Obter o product_type do item, se disponível no request
        $productType = $request->input('product_type', null);
        
        $query = [
            'user_id' => Auth::id(),
            'product_id' => $id,
        ];
        
        // Adicionar product_type à consulta se estiver disponível
        if ($productType) {
            $query['product_type'] = $productType;
        }
        
        $wantlistItem = Wantlist::where($query)->first();
        
        if ($wantlistItem) {
            $wantlistItem->delete();
            $message = 'Item removido da lista de notificações';
            $success = true;
        } else {
            $message = 'Item não encontrado na lista de notificações';
            $success = false;
        }
        
        // Obter a contagem atualizada de itens na wantlist
        $wantlist_count = Wantlist::where('user_id', Auth::id())->count();
        
        return response()->json([
            'success' => $success,
            'message' => $message,
            'wantlist_count' => $wantlist_count,
        ]);
    }
}
