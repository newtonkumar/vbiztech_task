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

    public function payment($id, Request $request) {
        $intent = auth()->user()->createSetupIntent();
        $product_id =  $id;
        $productData = Product::whereId($id)->first();

        return view('payment', compact('intent', 'product_id', 'productData'));
    }

    public function charge(Request $request) {
        if ($request->isMethod('post')) {
            $amount = (int)($request->amount) * 100;
            $paymentMethod = $request->payment_method;
            
            $authUser = auth()->user();
            $authUser->createOrGetStripeCustomer();
            $paymentMethodD = $authUser->addPaymentMethod($paymentMethod);
            $authUser->charge($amount, $paymentMethodD->id, ['off_session' => true, 'currency' => 'INR']);

            return redirect('/list')->with('status', "Payment done successfully");
        }
    }
}