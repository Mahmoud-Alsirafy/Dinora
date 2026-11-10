<?php

namespace App\Http\Controllers\Api\Var;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ApiVariants extends Controller
{
    public function show($sku, Request $request)
    {
        $type = $request->user()?->type ?? 'customer';

        $variant = ProductVariant::where('sku', $sku)
            ->active()
            ->with('prices')
            ->firstOrFail();
        if ($type !== 'admin') {
            $price = $variant->prices->firstWhere('customer_type', $type);
        } else {
            $price = $variant->prices;
        }
        return response()->json([
            'variant' => $variant->makeHidden('prices'),
            'price' => $price,
            'type' => $type,
        ], 200);
    }
}