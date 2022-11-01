<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->

    @include('admin.css')
 <style type="text/css">
    .title_deg
    {
       text-align: center;
       font-size: 25px; 
       font-weight: bold; 
       padding-bottom: 40px;
    }
    .table_deg
    {
        border: 2px solid white;
        width: 100%;
        margin: auto;
        text-align: center;

    }
    .th_deg
    {
        background-color: skyblue; 
    }
    .img_size
    {
        width: 200px;
        height: 100;

    }
</style>
</head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('admin.sidebar')
      <!-- partial -->
      @include('admin.header')
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">

                <h1 class="title_deg">All Orders</h1>

                <table class="table_deg">
                    <tr class="th_deg">
                        <th>Name</th>
                        <th>Emal</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Product Title</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                        <th>Image</th>
                    </tr>
                    @foreach ($order as $order)
                        
                    <tr >
                        <th>{{$order->name}}</th>
                        <th>{{$order->email}}</th>
                        <th>{{$order->address}}</th>
                        <th>{{$order->phone}}</th>
                        <th>{{$order->product_title}}</th>
                        <th>{{$order->quantity}}</th>
                        <th>{{$order->price}}</th>
                        <th>{{$order->payment_status}}</th>
                        <th>{{$order->delivery_status}}</th>
                        <th>
                            <img class="img_size" src="/product/{{$order->image}}" alt="">
                        </th>
                    </tr>

                    @endforeach
                </table>
            
            </div>
        </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    @include('admin.script')
    <!-- End custom js for this page -->
  </body>
</html>