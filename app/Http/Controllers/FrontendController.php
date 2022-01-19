<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mail;

class FrontendController extends Controller
{
    public function index()
    {

        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $counts = Cart::where('user_id', '=', $users->id)->count();



        $sell_products = Product::where('sold', '>', 5)->orderBy('sold', 'DESC')->limit(8)->paginate(8);
        $new_products  = Product::orderBy('created_at', 'DESC')->paginate(8);
        $categories    = Category::all();

        $params = [
            'new_products'  => $new_products,
            'sell_products' => $sell_products,
            'categories'    => $categories,
            'users'         => $users,
            'counts'        => $counts

        ];

        return view('frontend.layouts.home', $params);
    }
    public function category($id)
    {
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }

        $counts = Cart::where('user_id', '=', $users->id)->count();

        $categories      = Category::all();
        $products        = Product::where('category_id', '=', $id)->orderBy('created_at', 'DESC')->paginate(8);
        $categoryCurrent = Category::where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
        // $new_products  = Product::orderBy('created_at', 'DESC')->paginate(8);
        // dd($categories);
        $params = [
            'products'        => $products,
            'categories'      => $categories,
            'categoryCurrent' => $categoryCurrent,
            'users'           => $users,
            'counts'          => $counts
        ];

        return view('frontend.categories', $params);
    }
    public function detail($id)
    {
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $counts = Cart::where('user_id', '=', $users->id)->count();
        $comments = Comment::where('com_product', $id)->get();
        $products  = Product::where('id', '=', $id)->first();
        $categories      = Category::all();
        $params    = [
            'products'      => $products,
            'categories'    => $categories,
            'users'         => $users,
            'comments'      => $comments,
            'counts'        => $counts
        ];
        return view('frontend.detail', $params);
    }
    public function cart()
    {
        $total = 0;
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $counts = Cart::where('user_id', '=', $users->id)->count();

        $carts = DB::table('carts')
            ->join('product', 'product.id', '=', 'carts.product_id')
            ->select(
                'carts.*',
                'product.image',
                "product.name",
                DB::raw('(carts.price * carts.quantity) as totalPrice'),
                'carts.quantity'
            )
            ->where('carts.user_id', '=', $users->id)
            ->get();
        foreach ($carts as $cart) {
            $total += $cart->totalPrice;
        }
        $categories      = Category::all();
        $params = [
            'carts'      => $carts,
            'categories' => $categories,
            'users'      => $users,
            'total'      => $total,
            'counts'     => $counts
        ];
        return view('frontend.cart', $params);
    }
    public function addToCart($id)
    {
        $product = Product::find($id);
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $cart_product = Cart::where('product_id', '=', $product->id)->where('user_id', '=', $users->id)->first();
        if ($cart_product) {
            $cart_product->quantity += 1;
            $cart_product->save();
        } else {
            $carts = new Cart();
            $carts->user_id    = $users->id;
            $carts->product_id = $product->id;
            $carts->quantity   = 1;
            $carts->price      = $product->price;
            $carts->save();
        }
        return redirect()->route('cart');
    }
    public function postComment(Request $request, $id)
    {

        $comments = new Comment();
        $comments->com_name     = $request->com_name;
        $comments->com_email    = $request->email;
        $comments->com_content  = $request->content;
        $comments->com_product  = $id;
        $comments->save();
        return back();
    }
    public function getSearch(Request $request)
    {
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $categories      = Category::all();
        $search = $request->search;
        if ($search) {
            $search = str_replace(' ', '%', $search);
            $products = Product::where('name', 'like', '%' . $search . '%')->get();
        } else {
            $products = Product::all();
        }

        $params = [
            'products'    => $products,
            'search_name' => $search,
            'categories'  => $categories,
            'users'       => $users
        ];
        return view('frontend.search', $params);
    }
    public function checkout()
    {
        $total = 0;
        if (Auth::user()) {
            $users = Auth::user();
        } else {
            $users = User::find(1);
        }
        $counts = Cart::where('user_id', '=', $users->id)->count();

        $carts = DB::table('carts')
            ->join('product', 'product.id', '=', 'carts.product_id')
            ->select(
                'carts.*',
                'product.image',
                "product.name",
                DB::raw('(carts.price * carts.quantity) as totalPrice'),
                'carts.quantity'
            )
            ->where('carts.user_id', '=', $users->id)
            ->get();
        foreach ($carts as $cart) {
            $total += $cart->totalPrice;
        }
        $categories      = Category::all();
        $params = [
            'carts'      => $carts,
            'categories' => $categories,
            'users'      => $users,
            'total'      => $total,
            'counts'     => $counts
        ];
        return view('frontend.checkout', $params);
    }
    public function complete(Request $request)
    {

        $messages = [
            'update_at.required' => 'bạn chưa chọn ngày dự kiến nhận hàng ',
        ];
        $roles = [
            'update_at' => 'required',
        ];
        $this->validate($request, $roles, $messages);
        $user         = Auth::user();
        if (!$user) {
            return redirect()->route('home.login');
        }
        $carts = DB::table('carts')
            ->join('product', 'product.id', '=', 'carts.product_id')
            ->select(
                'carts.*',
                'product.image',
                "product.name",
                DB::raw('(carts.price * carts.quantity) as totalPrice'),
                'carts.quantity')
            ->where('carts.user_id', '=', $user->id)
            ->get();

        $totalPriceCarts =   DB::table('carts')
            ->select('user_id', DB::raw('sum(carts.price * carts.quantity) as totalPrice'))
            ->where('carts.user_id', '=', $user->id)
            ->groupBy('carts.user_id')
            ->first();

      
        $orders                = new Order();
        $orders->created_at    = date('Y-m-d');
        $orders->updated_at    = $request->update_at;
        $orders->user_id       = $user->id;
        $orders->code       = time();
        $orders->total   = $totalPriceCarts->totalPrice;
        $orders->status        = "đang xử lý";
        $orders->save();

        $order_id = DB::getPdo()->lastInsertId();
        foreach ($carts as $key => $cart) {
            $order_details = new Orderdetail();
            $order_details->product_id    = $cart->product_id;
            $order_details->price = $cart->price;
            $order_details->order_id  = $order_id;
            $order_details->total  = $cart->totalPrice;
            $order_details->quantity   = $cart->quantity;
            $order_details->save();
        }
        Session::flash('success', 'cảm ơn bạn đã mua sản phẩm của chúng tôi');
        $cartUserDelete = Cart::where('user_id', '=', $user->id);
        $cartUserDelete->delete();
        return redirect()->route('home');
    }
}
