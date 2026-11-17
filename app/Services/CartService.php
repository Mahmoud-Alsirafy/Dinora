<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CartService
{
    private const CART_TTL = 30;
    private const LOCK_TTL = 30;
    private const LOCK_WAIT = 5;

    private function resolveCart(Request $request, bool $createToken = false): array
    {
        $user = $request->user('sanctum');

        if ($user) {
            return [
                'cache_key' => "cart_user_{$user->id}",
                'cart_token' => null,
            ];
        }

        $cartToken = $request->header('X-Cart-Token');

        if (empty($cartToken) && $createToken) {
            $cartToken = Str::uuid()->toString();
        }

        if (empty($cartToken)) {
            abort(400, 'X-Cart-Token is required');
        }

        return [
            'cache_key' => "cart_guest_{$cartToken}",
            'cart_token' => $cartToken,
        ];
    }

    private function withCartLocks(array $cacheKeys, Closure $callback)
    {
        $cacheKeys = array_unique($cacheKeys);
        sort($cacheKeys);

        $locks = [];

        try {
            foreach ($cacheKeys as $key) {
                $lock = Cache::lock(
                    "{$key}:lock",
                    self::LOCK_TTL
                );

                $lock->block(self::LOCK_WAIT);

                $locks[] = $lock;
            }

            return $callback();
        } finally {
            foreach (array_reverse($locks) as $lock) {
                $lock->release();
            }
        }
    }

    public function getCart(Request $request)
    {
        $context = $this->resolveCart($request, true);

        $cart = Cache::get($context['cache_key'], []);

        return [
            'cart' => $cart,
            'cart_token' => $context['cart_token'],
        ];
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $context = $this->resolveCart($request, true);

        $cacheKey = $context['cache_key'];

        return $this->withCartLocks(
            [$cacheKey],
            function () use ($cacheKey, $context, $validated) {

                $cart = Cache::get($cacheKey, []);

                $variantId = $validated['variant_id'];
                $quantity = $validated['quantity'];

                if (isset($cart[$variantId])) {
                    $cart[$variantId]['quantity'] += $quantity;
                } else {
                    $cart[$variantId] = [
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                    ];
                }

                $saved = Cache::put(
                    $cacheKey,
                    $cart,
                    now()->addDays(self::CART_TTL)
                );

                if (!$saved) {
                    throw new \RuntimeException('Failed to save cart');
                }

                return [
                    'cart' => $cart,
                    'cart_token' => $context['cart_token'],
                ];
            }
        );
    }

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $context = $this->resolveCart($request);

        $cacheKey = $context['cache_key'];

        return $this->withCartLocks(
            [$cacheKey],
            function () use ($cacheKey, $context, $validated, $id) {

                $cart = Cache::get($cacheKey, []);
                $variant  = ProductVariant::findOrFail($id);
                $itemId = $variant->id;

                if (!isset($cart[$itemId])) {
                    abort(404, 'Item not found in cart');
                }

                $cart[$itemId]['quantity'] = $validated['quantity'];

                $saved = Cache::put(
                    $cacheKey,
                    $cart,
                    now()->addDays(self::CART_TTL)
                );

                if (!$saved) {
                    throw new \RuntimeException('Failed to update cart');
                }

                return [
                    'cart' => $cart,
                    'cart_token' => $context['cart_token'],
                ];
            }
        );
    }

    public function mergeCart(Request $request, User $user)
    {
        $cartToken = $request->header('X-Cart-Token');

        if (empty($cartToken)) {
            return false;
        }

        $userKey = "cart_user_{$user->id}";
        $guestKey = "cart_guest_{$cartToken}";

        return $this->withCartLocks(
            [$userKey, $guestKey],
            function () use ($userKey, $guestKey) {

                $userCart = Cache::get($userKey, []);
                $guestCart = Cache::get($guestKey, []);

                if (empty($guestCart)) {
                    return false;
                }

                foreach ($guestCart as $key => $value) {
                    if (isset($userCart[$key])) {
                        $userCart[$key]['quantity'] += $value['quantity'];
                    } else {
                        $userCart[$key] = $value;
                    }
                }

                $saved = Cache::put(
                    $userKey,
                    $userCart,
                    now()->addDays(self::CART_TTL)
                );

                if (!$saved) {
                    throw new \RuntimeException('Failed to merge carts');
                }

                Cache::forget($guestKey);

                return true;
            }
        );
    }
}