<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->

    <base href="/public">
    @include('admin.css')
 
    <style>
        .div_center{
            text-align: center;
            padding-top: 40px; 
        }
        .font_size{
            font-size: 40px;
            padding-bottom: 40px;
        }
        .text_color{
            color: black;
        }
        label{
            display: inline-block;
            width: 200px;
        }
        .div_design
        {
            padding-bottom: 15px;
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

                
              @if (session()->has('message'))
              
              <div class="alert alert-success">

                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>

                {{ session()->get('message') }}
              </div>

              @endif

                <div class="div_center">
                    <h1 class="font_size">Update Product</h1>

                    <form action="{{ url('/update_product_confirm',$product->id) }}" method="POST" enctype="multipart/form-data">

                        @csrf

                    <div class="div_design">
                        <label>Product Title :</label>
                        <input class="text_color" type="text" name="title" placeholder="Write a title" value="{{$product->title}}" required>
                    </div>

                    <div class="div_design">
                        <label>Product Description: </label>
                        <input class="text_color" type="text" name="description" placeholder="Write a description" value="{{$product->description}}" required>
                    </div>

                    <div class="div_design">
                        <label>Product Price :</label>
                        <input class="text_color" type="number" name="price" placeholder="Write a Price" value="{{$product->price}}" required>
                    </div>

                    <div class="div_design">
                        <label>Discount Price :</label>
                        <input class="text_color" type="number" name="dis_price" placeholder="Write a Discount " value="{{ $product->discount_price }}">
                    </div>

                    <div class="div_design">
                        <label>Product Quantity :</label>
                        <input class="text_color" type="number" min="0" name="quantity"  placeholder="Write a quantity" value="{{ $product->quantity }}" required>
                    </div>

                    <div class="div_design">
                        <label>Product Category :</label>
                        <select class="text_color" name="category" id="" required>
                            <option value="{{ $product->category }}" selected>{{ $product->category }}</option>
                            
                            @foreach ($category as $category)
                        
                            <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                        
                            @endforeach
                        
                        </select>
                    </div>


                    <div class="div_design">
                        <label>Current Product Image :</label>
                        <img style="margin:auto" height="100" width="100" src="/product/{{$product->image}}" alt="">
                    </div>


                    <div class="div_design">
                        <label>Change Product Image :</label>
                        <input type="file" name="image">
                    </div>

                    <div  class="div_design">
                        <input type="submit" value="Update Product" class="btn btn-primary">
                    </div>
                </form>

                </div>

            </div>
        </div>
        <!-- plugins:js -->
    @include('admin.script')
    <!-- End custom js for this page -->
  </body>
</html>