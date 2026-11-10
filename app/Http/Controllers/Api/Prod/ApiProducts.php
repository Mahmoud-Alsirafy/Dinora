<?php

namespace App\Http\Controllers\Api\Prod;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiProducts extends Controller
{
    public function get($id)
    {
        $page = request()->get('page', 1);

        $products = Cache::remember(
            "category_{$id}_products_page_{$page}",
            3600,
            fn() => Product::active()
                ->where('cate_id', $id)
                ->whereHas('category', function ($query) {
                    $query->active();
                })
                ->with([
                    'variants' => function ($query) {
                        $query->active();
                    }
                ])
                ->paginate(3)
        );

        return response()->json([
            'products' => $products
        ], 200);
    }
    public function show($slug)
    {
        // return $slug;
        $product = Product::where('slug', $slug)->active()->with([
            'variants' => function ($query) {
                $query->active();
            }
        ])->firstOrFail();

        return response()->json([
            'product' => $product
        ], 200);
    }
}
