<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CartService
{
    private $user, $user_type, $cache_key, $cart_token = null;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->user_type = $this->user?->type ?? 'guest';
    }

    public function getCart(Request $request)
    {
        if ($this->user_type == 'guest') {
            $this->cart_token = $request->header('X-Cart-Token') ?? Str::uuid()->toString();
            $this->cache_key = "cart_guest_{$this->cart_token}";
        } else {
            $this->cache_key = "cart_user_{$this->user?->id}";
        }
        $cart =  Cache::get($this->cache_key, []);
        return [
            'cart' => $cart,
            'cart_token' => $this->cart_token,
        ];
    }

    public function addItem(Request $request)
    {

        $data = $this->getCart($request);
        $cart = $data['cart'];

        // dd([
        //     'cache_key' => $this->cache_key,
        //     'cart_token' => $data['cart_token'],
        //     'cart' => $cart,
        // ]);

        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity');

        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = [
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        Cache::put($this->cache_key, $cart, now()->addDays(30));

        return $this->getCart($request);
    }
}