<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Image;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function index()
    {
        $data = Image::where('enable',1)->with('product')->orderBy('name','ASC')->get();

        return ResponseFormatter::success($data,'Get All Image');
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name.*'        => 'required',
            'description.*' => 'required',
            'enable.*'      => 'required|boolean',
            'product_id'    => 'required|integer',
            'image.*'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {

            DB::beginTransaction();

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
                    $images->name   = request()->name[$key];
                    $images->file   = $pathfile;
                    $images->enable = request()->enable[$key];
                    $images->save();

                    // Store Pivot Image
                    $productImage               = new ProductImage;
                    $productImage->product_id   = request()->product_id;
                    $productImage->image_id     = $images->id;
                    $productImage->save();
                }
            }

            DB::commit();

            return ResponseFormatter::success(null,'Store Image Success');

        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function updateMultiple()
    {
        $validator = Validator::make(request()->all(), [
            'name.*'        => 'required',
            'description.*' => 'required',
            'enable.*'      => 'required|boolean',
            'product_id'    => 'required|integer',
            'image_id.*'    => 'required|integer',
            'image.*'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {

            DB::beginTransaction();

            $products = ProductImage::where('product_id', request()->product_id)->get()->pluck('image_id')->toArray();
            if (request()->has('image')) {
                foreach (request()->image as $key => $image) {
                    $status = (in_array(request()->image_id[$key], $products)) ? true : false;

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
                        $images->name   = request()->name[$key];
                        $images->file   = $pathfile;
                        $images->enable = request()->enable[$key];
                        $images->save();

                        // Update Pivot Image
                        $productImage               = new ProductImage;
                        $productImage->product_id   = request()->product_id;
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
                        $images->name   = request()->name[$key];
                        $images->file   = $pathfile;
                        $images->enable = request()->enable[$key];
                        $images->save();
                    }
                }
            }

            DB::commit();

            return ResponseFormatter::success(null,'Product Successfully Updated');

        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            'name'        => 'required',
            'description' => 'required',
            'enable'      => 'required|boolean',
            'image_id'    => 'required|integer',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {

            DB::beginTransaction();

            if (request()->has('image')) {
                $image = request()->file('image');

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

                $images         = Image::find(request()->image_id);
                $images->name   = request()->name;
                $images->file   = $pathfile;
                $images->enable = request()->enable;
                $images->save();
            }

            DB::commit();

            return ResponseFormatter::success($images,'Image Successfully Updated');

        } catch (\Throwable $th) {
            DB::rollBack();

            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function destroy()
    {
        $image = Image::find(request()->image_id);
        if($image){
            ProductImage::where('image_id', request()->image_id)->get()->each(function ($item){
                $item->delete();
            });
            $image->delete();

            return ResponseFormatter::success($image,'Image Success Deleted');
        }else{
            return ResponseFormatter::error(null,'Image does not exist',500);
        }
    }
}
