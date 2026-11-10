<?php

namespace App\Http\Controllers\Api\Cate;

use App\Http\Controllers\Controller;
use App\Models\Categoie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Categories extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);

        $products = Cache::remember(
            "products_page_{$page}",
            3600,
            fn() => Categoie::active()->paginate(3)
        );

        return response()->json([
            'products' => $products
        ], 200);
    }
}
