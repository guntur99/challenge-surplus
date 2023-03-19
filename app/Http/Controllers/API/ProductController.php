<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\ProductImage;
use App\Models\Image;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $data = Product::where('enable',1)->with(['category','images'])->orderBy('name','ASC')->get();

        return ResponseFormatter::success($data,'Get All Product');
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name'                => 'required',
            'description'         => 'required',
            'enable'              => 'required|boolean',
            'category_id.*'       => 'required|integer',
            'image_name.*'        => 'required',
            'image_description.*' => 'required',
            'image_enable.*'      => 'required|boolean',
            'image.*'             => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {

            DB::beginTransaction();

            // Store Product
            $product                = new Product;
            $product->name          = request()->name;
            $product->description   = request()->description;
            $product->enable        = request()->enable;
            $product->save();

            // Store Pivot Category
            foreach (request()->category_id as $key => $id) {
                $exist = Category::where('id', $id)->where('enable', 1)->get();
                if ($exist) {
                    $categoryProduct                = new CategoryProduct;
                    $categoryProduct->category_id   = $id;
                    $categoryProduct->product_id    = $product->id;
                    $categoryProduct->save();
                }
            }

            // Store Image Product
            if (request()->has('image')) {
                foreach (request()->image as $key => $image) {
                    if ($image->getSize() > 2048000) {
                        return back()->with('error', 'The image must not be greater than 2 MB.');
                    }
                    $fileImage       = explode('.', $image->getClientOriginalName());
                    $name            = $fileImage[0];
                    $fileType        = $fileImage[1];
                    $imageName       = 'product-' . date('Y-m-d') . rand(1111, 9999) . '.' . $fileType;
                    $destinationPath = 'assets/images/product/';

                    $image->move($destinationPath, $imageName);
                    $pathfile    = $destinationPath . $imageName;

                    $images         = new Image;
                    $images->name   = request()->image_name[$key];
                    $images->file   = $pathfile;
                    $images->enable = request()->image_enable[$key];
                    $images->save();

                    // Store Pivot Image
                    $productImage               = new ProductImage;
                    $productImage->product_id   = $product->id;
                    $productImage->image_id     = $images->id;
                    $productImage->save();
                }
            }

            DB::commit();

            return ResponseFormatter::success($product,'Store Product Success');

        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            'name'                => 'required',
            'description'         => 'required',
            'enable'              => 'required|boolean',
            'category_id.*'       => 'required|integer',
            'image_name.*'        => 'required',
            'image_description.*' => 'required',
            'image_enable.*'      => 'required|boolean',
            'image_id.*'          => 'required|integer',
            'image.*'             => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {

            DB::beginTransaction();

            // Update Product
            $product                = Product::find(request()->product_id);
            $product->name          = request()->name;
            $product->description   = request()->description;
            $product->enable        = request()->enable;
            $product->save();

            $categories = CategoryProduct::where('product_id', $product->id)->get()->pluck('category_id')->toArray();

            // Update Pivot Category
            foreach (request()->category_id as $key => $catId) {
                $status = (in_array($catId, $categories)) ? true : false;
                $exist  = Category::where('id', $catId)->where('enable', 1)->first();
                if (!$status) {
                    $categoryProduct                = new CategoryProduct;
                    $categoryProduct->category_id   = $exist->id;
                    $categoryProduct->product_id    = $product->id;
                    $categoryProduct->save();
                }
            }

            // Update Image Product
            $productIds = ProductImage::where('product_id', $product->id)->get()->pluck('image_id')->toArray();
            if (request()->has('image')) {
                foreach (request()->image as $key => $image) {
                    $status = (in_array(request()->image_id[$key], $productIds)) ? true : false;

                    if ($image->getSize() > 2048000) {
                        return back()->with('error', 'The image must not be greater than 2 MB.');
                    }

                    if (!$status) {
                        $fileImage       = explode('.', $image->getClientOriginalName());
                        $name            = $fileImage[0];
                        $fileType        = $fileImage[1];
                        $imageName       = 'product-' . date('Y-m-d') . rand(1111, 9999) . '.' . $fileType;
                        $destinationPath = 'assets/images/product/';

                        $image->move($destinationPath, $imageName);
                        $pathfile    = $destinationPath . $imageName;

                        $images         = new Image;
                        $images->name   = request()->image_name[$key];
                        $images->file   = $pathfile;
                        $images->enable = request()->image_enable[$key];
                        $images->save();

                        // Update Pivot Image
                        $productImage               = new ProductImage;
                        $productImage->product_id   = $product->id;
                        $productImage->image_id     = $images->id;
                        $productImage->save();
                    }else{

                        $fileImage       = explode('.', $image->getClientOriginalName());
                        $name            = $fileImage[0];
                        $fileType        = $fileImage[1];
                        $imageName       = 'product-' . date('Y-m-d') . rand(1111, 9999) . '.' . $fileType;
                        $destinationPath = 'assets/images/product/';

                        $image->move($destinationPath, $imageName);
                        $pathfile    = $destinationPath . $imageName;

                        $images         = Image::find(request()->image_id[$key]);
                        $images->name   = request()->image_name[$key];
                        $images->file   = $pathfile;
                        $images->enable = request()->image_enable[$key];
                        $images->save();
                    }
                }
            }

            DB::commit();

            return ResponseFormatter::success($product,'Product Successfully Updated');

        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function destroy()
    {
        $product = Product::find(request()->product_id);
        if($product){
            ProductImage::where('product_id', request()->product_id)->get()->each(function ($item){
                $image = Image::find($item->id);
                $image->delete();
                $item->delete();
            });
            $product->delete();

            return ResponseFormatter::success($product,'Product Success Deleted');
        }else{
            return ResponseFormatter::error(null,'Product does not exist',500);
        }
    }
}
