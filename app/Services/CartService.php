<?php

namespace App\Services;

use App\Models\User;
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
        $user = $request->user('sanctum');

        if ($user) {
            $this->cache_key = "cart_user_{$user->id}";
            $this->cart_token = null;
        } else {
            $this->cart_token = $request->header('X-Cart-Token') ?? Str::uuid()->toString();
            $this->cache_key = "cart_guest_{$this->cart_token}";
        }

        $cart = Cache::get($this->cache_key, []);

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

    public function mergeCart(Request $request, User $user)
    {
        $cart_token = $request->header('X-Cart-Token');

        if (empty($cart_token)) {
            return;
        }

        $user_key = "cart_user_{$user->id}";
        $guest_key = "cart_guest_{$cart_token}";



        $user_cart = Cache::get($user_key, []);
        $guest_cart = Cache::get($guest_key, []);
        if (empty($guest_cart)) {
            return;
        }

        foreach ($guest_cart as $key => $value) {
            if (isset($user_cart[$key])) {
                $user_cart[$key]['quantity'] += $value['quantity'];
            } else {
                $user_cart[$key] = $value;
            }
        }
        $store_cache = Cache::put($user_key, $user_cart, now()->addDays(30));
        if ($store_cache) {
            Cache::forget($guest_key);
        }

        return $store_cache;
    }
}