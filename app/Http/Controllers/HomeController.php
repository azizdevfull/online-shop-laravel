<?php

namespace App\Http\Controllers;

use App\Models\Cart;

use App\Models\User;

use App\Models\Order;

use App\Models\Product;
use Illuminate\Contracts\Session\Session as SessionSession;

use function Ramsey\Uuid\v1;
use Illuminate\Http\Request;
use Session;
use Stripe;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index(){
        $product = Product::paginate(10);
        return view('home.userpage', compact('product'));
    }

    public function redirect()
    {
        $usertype = Auth::user()->usertype;

        if ($usertype == '1')
        {

            return view('admin.home');

        }
        else
        {
        $product = Product::paginate(10);
        return view('home.userpage', compact('product'));
        }

    }
    public function product_details($id)
    {
        $product = Product::find($id);
        return view('home.product_details',compact('product'));
    }

    public function add_cart(Request $request, $id)
    {
        // $product = Product::find($id);
        if (Auth::id()) 
        {

            $user = Auth::user();

            $product = Product::find($id);

            $cart = new Cart;
            
            $cart->name = $user->name; 
            
            $cart->email = $user->email; 
            
            $cart->phone = $user->phone; 
            
            $cart->address = $user->address; 

            $cart->user_id = $user->id;

            $cart->product_title = $product->title;


            if($product->discount_price != null)
            {
            
                $cart->price = $product->discount_price * $request->quantity;
                
            }
            else
            {
                $cart->price = $product->price * $request->quantity;
            }


            $cart->image = $product->image;

            $cart->Product_id = $product->id;

            $cart->quantity = $request->quantity;

            $cart->save();
            
            return redirect()->back();
            
        
        }
        else{
            return redirect('login');
        }
    }

    public function show_cart()
    {
        if (Auth::id()) {
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            return view('home.showcart', compact('cart'));
        }
        else {
            return redirect('login');
        }

    }

    public function remove_cart($id)
    {
        $cart = Cart::find($id);

        $cart->delete();
        
        return redirect()->back();
    }

    public function cash_order()
    {
        $user = Auth::user();
        $userid = $user->id;

        $data = cart::where('user_id','=',$userid)->get();
        
        foreach ($data as $data) {
            $order = new Order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;
            $order->product_title = $data->product_title;
            $order->price = $data->price;
            $order->quantity = $data->quantity;
            $order->image = $data->image;
            $order->product_id = $data->Product_id;

            $order->payment_status='cash on delivery';

            $order->delivery_status='processing';

            $order->save();

            $cart_id = $data->id;

            $cart = Cart::find($cart_id);

            $cart->delete();
        }

        return redirect()->back()->with('message', 'We Received Your Order. We Will Connect With You Soon!');
        
    }

    public function stripe($totalprice)
    {
        return view('home.stripe',compact('totalprice'));
    }

    public function stripePost(Request $request, $totalprice)
    {

        // dd($totalprice);
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Stripe\Charge::create ([
                "amount" => $totalprice * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Thanks For Payment." 
        ]);


        $user = Auth::user();
        $userid = $user->id;

        $data = cart::where('user_id','=',$userid)->get();
        
        foreach ($data as $data) {
            $order = new Order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;
            $order->product_title = $data->product_title;
            $order->price = $data->price;
            $order->quantity = $data->quantity;
            $order->image = $data->image;
            $order->product_id = $data->Product_id;

            $order->payment_status='Paid';

            $order->delivery_status='processing';

            $order->save();

            $cart_id = $data->id;

            $cart = Cart::find($cart_id);

            $cart->delete();
        }

        // Session::flash('success', 'Payment successful!');
        return redirect()->back()->with('success', 'Payment successful!');
              
        // return back();
    }
}
