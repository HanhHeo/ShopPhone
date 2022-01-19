<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\FrontendUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderdetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LoginAdmin;
use App\Http\Middleware\LoginUser;
use Illuminate\Support\Facades\Route;

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

require_once 'frontend.php';

//CRUD Admin
Route::middleware([LoginAdmin::class])->prefix("admin")->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('orderdetail', OrderdetailController::class);
    Route::resource('employees', EmployeeController::class);
});

//đăng nhập,đăng ký

Route::get('login', [LoginController::class, 'show_login'])->name('auth.login');
Route::post('postlogin', [LoginController::class, 'handlle_login'])->name('auth.postlogin');
Route::get('register', [LoginController::class, 'register'])->name('auth.register');
Route::post('postregister', [LoginController::class, 'handle_register'])->name('auth.postregister');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');




Route::get('homeusers', [FrontendUserController::class, 'index'])->name('homeUsers');


Route::middleware([LoginUser::class])->prefix("users")->group(function () {
    Route::get('home', [FrontendController::class, 'index'])->name('home');
    Route::get('search', [FrontendController::class, 'getSearch'])->name('search');
    Route::get('category/{id}', [FrontendController::class, 'category'])->name('category');
    Route::get('detail/{id}', [FrontendController::class, 'detail'])->name('detail');
    Route::post('detail/{id}', [FrontendController::class, 'postComment'])->name('comment');
    Route::get('cart', [FrontendController::class, 'cart'])->name('cart');
    Route::get('addtocart/{id}', [FrontendController::class, 'addToCart'])->name('addtocart');
    Route::post('editcart', [FrontendUserController::class, 'edit_cart'])->name('editcart');
    Route::get('delete/{id}', [FrontendUserController::class, 'delete_cart'])->name('deletecart');
    Route::post('show', [FrontendController::class, 'postEmail'])->name('show');
    Route::get('checkout', [FrontendController::class, 'checkout'])->name('checkout');
    Route::post('pay', [FrontendController::class, 'complete'])->name('pay');
});
