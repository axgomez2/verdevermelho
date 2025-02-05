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

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_type' => 'required|string',
        ]);

        $wishlistItem = Wishlist::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'product_type' => $request->product_type,
        ])->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $message = 'Item removido dos favoritos';
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'product_type' => $request->product_type,
            ]);
            $message = 'Item adicionado aos favoritos';
            $added = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added' => $added
        ]);
    }
}
