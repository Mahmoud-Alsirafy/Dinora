<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class ApiCart extends Controller
{
    private $cartService;
    public function __construct(CartService $cart)
    {
        $this->cartService = $cart;
    }
    public function index(Request $request)
    {
        $data = $this->cartService->getCart($request);
        $response = response()->json([
            'data' => $data,
            'message' => 'Cart retrieved successfully',
            'status' => 200,
        ], 200);

        if (!empty($data['cart_token'])) {
            $response->header('X-Cart-Token', $data['cart_token']);
        }

        return $response;
    }
    public function store_item(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $data = $this->cartService->addItem($request);
        return response()->json([
            'data' => $data,
            'message' => 'Item added to cart successfully',
            'status' => 200,
        ], 200);
    }
}