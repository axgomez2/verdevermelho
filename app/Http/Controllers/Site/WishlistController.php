<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with(['product'])
            ->get()
            ->map(function ($item) {
                return $item->product;
            });

        return view('site.wishlist.index', compact('wishlistItems'));
    }

    public function toggle($type, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, faça login para gerenciar seus favoritos'
            ], 401);
        }

        $wishlistItem = Wishlist::where([
            'user_id' => Auth::id(),
            'product_id' => $id,
            'product_type' => $type,
        ])->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $message = 'Item removido dos favoritos';
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $id,
                'product_type' => $type,
            ]);
            $message = 'Item adicionado aos favoritos';
            $added = true;
        }

        // Pega a contagem atualizada dos favoritos
        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'added' => $added,
            'wishlistCount' => $wishlistCount
        ]);
    }
}

