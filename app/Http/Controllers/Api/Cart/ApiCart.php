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
        ], 200);

        if (!empty($data['cart_token'])) {
            $response->header('X-Cart-Token', $data['cart_token']);
        }

        return $response;
    }
    public function store_item(Request $request)
    {
        $data = $this->cartService->addItem($request);
        return response()->json([
            'data' => $data,
            'message' => 'Item added to cart successfully',
        ], 200);
    }

    public function cart_update(Request $request, $id)
    {
        $data = $this->cartService->updateItem($request, $id);
        return response()->json([
            'data' => $data,
            'message' => 'Item updated to cart successfully',
        ], 200);
    }
}