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

    public function payment(Request $request) {
        if ($request->isMethod('post')) {
            $amount = $request->price;
            $paymentMethod = $request->payment_method;

            $authUser = auth()->user();
            $authUser->charge($amount, $paymentMethod);
        }

        return view('list');
    }
}