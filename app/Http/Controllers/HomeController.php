<?php

namespace App\Http\Controllers;

use Stripe;

use Session;

use App\Models\Cart;

use App\Models\User;
use App\Models\Order;

use App\Models\Reply;
use App\Models\Comment;
use App\Models\Product;
use function Ramsey\Uuid\v1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

use Illuminate\Contracts\Session\Session as SessionSession;

class HomeController extends Controller
{
    public function __construct()
    {
        if(Auth::user()){
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            
        }else{
            $cart = [];
        }
    }

    public function index(){
        $product = Product::paginate(10);
        $comment = Comment::orderby('id', 'desc')->get();
        $reply = Reply::all();
        if(Auth::user()){
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            
        }else{
            $cart = [];
        }


        return view('home.userpage', compact('product','comment','reply', 'cart'));
    }

    public function redirect()
    {

        $usertype = Auth::user()->usertype;

        if ($usertype == '1')
        {
            $total_product = Product::all()->count();
            $total_order = Order::all()->count();
            $total_user = User::all()->count();
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();

            $order = Order::all();

            $total_revenue = 0;

            foreach($order as $order)
            {
                $total_revenue = $total_revenue + $order->price;
            }

            $total_delivered = Order::where('delivery_status','=','delivered')->get()->count();

            $total_processing = Order::where('delivery_status','=','processing')->get()->count();

            return view('admin.home', compact('total_product', 'total_order', 'total_user', 'total_revenue','total_delivered', 'total_processing', 'cart'));

        }
        else
        {
            $product = Product::paginate(10);
            $comment = Comment::orderby('id', 'desc')->get();
            $reply = Reply::all();
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();


        return view('home.userpage', compact('product', 'comment', 'reply', 'cart'));
        }

    }
    public function product_details($id)
    {
        if(Auth::user()){
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            
        }else{
            $cart = [];
        }
        $product = Product::find($id);
        return view('home.product_details',compact('product', 'cart'));
    }

    public function add_cart(Request $request, $id)
    {
        // $product = Product::find($id);
        if (Auth::id()) 
        {

            $user = Auth::user();

            $userid = $user->id;

            $product = Product::find($id);

            $product_exist_id = Cart::where('Product_id', '=', $id)->where('user_id','=',$userid)->get('id')->first();

            if ($product_exist_id) 
            {
                
                $cart = Cart::find($product_exist_id)->first();

                $quantity = $cart->quantity;

                $cart->quantity=$quantity + $request->quantity;

                if($product->discount_price != null)
                {
                
                    $cart->price = $product->discount_price * $cart->quantity;
                    
                }
                else
                {
                    $cart->price = $product->price * $cart->quantity;
                }

                $cart->save();

                Alert::success('Product Added Successfully', 'We have added product to cart');

                return redirect()->back();
                
            }

            else 
            {

                
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
            
            Alert::success('Product Added Successfully', 'We have added product to cart');

            return redirect()->back();


            }
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
        
        Alert::warning('Cart Removed Successfully', 'We have remove cart!');
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
    public function show_order()
    {
            if(Auth::id()){
                $id = Auth::user()->id;
                $order = Order::where('user_id','=',$id)->get();
                return view('home.order', compact('order'));
            }
            else
            {
                return redirect('login');
            }
    }
    public function cancel_order($id)
    {
        $order = Order::find($id);
        
        $order->delivery_status = 'You Canceled The Order';
        
        $order->save();
        
        return redirect()->back();
    }

    public function add_comment(Request $request)
    {
        if(Auth::id())
        {
            $comment = new Comment;
            
            $comment->name = Auth::user()->name;
            
            $comment->user_id = Auth::user()->id;
            
            $comment->comment=$request->comment;

            $comment->save();

            return redirect()->back();
        }
        else{
            return redirect('login');
        }
    }

    public function add_reply(Request $request)
    {
        if(Auth::id())
        {
            $reply = new Reply;
            
            $reply->name = Auth::user()->name;
            
            $reply->user_id = Auth::user()->id;
            
            $reply->comment_id = $request->commentId;

            $reply->reply=$request->reply;

            $reply->save();

            return redirect()->back();
        }
        else{
            return redirect('login');
        }
    }
    public function product_search(Request $request)
    {
        $search_text = $request->search;
        $product = Product::where('title', 'LIKE',"%$search_text%")->orWhere('category', 'LIKE',"$search_text")->paginate(10);
        $comment = Comment::orderby('id', 'desc')->get();
        $reply = Reply::all();
        if(Auth::user()){
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            
        }else{
            $cart = [];
        }
        return view('home.userpage', compact('product','comment','reply','cart'));
    }
    public function product()
    {
        $product = Product::paginate(10);
        $comment = Comment::orderby('id', 'desc')->get();
        $reply = Reply::all();

        return view('home.all_product', compact('product','comment','reply'));   
    }

    public function search_product(Request $request)
    {
        $search_text = $request->search;
        $product = Product::where('title', 'LIKE',"%$search_text%")->orWhere('category', 'LIKE',"$search_text")->paginate(10);
        $comment = Comment::orderby('id', 'desc')->get();
        $reply = Reply::all();
        return view('home.all_product', compact('product','comment','reply'));
    }

}
