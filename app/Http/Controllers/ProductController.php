<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();
        
        return view('list', compact('products'));
    }

    public function payment($productId, Request $request) {
        $productData = Product::whereId($productId)->first();
        
        if ($request->isMethod('post')) {

        }

        return view('payment', compact('productData'));
    }
}