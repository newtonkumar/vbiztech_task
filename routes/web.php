<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/payment/{id}', function ($id) {
    return view('payment',[
        'intent' => auth()->user()->createSetupIntent(),
        'product_id' =>  $id,
        'productData' => Product::whereId($id)->first(),
    ]);
})->middleware(['auth'])->name('payment');

Route::post('/payment', function (Request $request) {
   return view('list', [
       'products' => Product::all(),
   ]);
})->middleware(['auth'])->name('payment');

require __DIR__.'/auth.php';

Route::any('/list',[ProductController::class, 'index']);
Route::any('pay/{id}',[ProductController::class, 'payment']);
