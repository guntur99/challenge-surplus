<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::where('enable',1)->with('products')->orderBy('name','ASC')->get();

        return ResponseFormatter::success($data,'Get All Category');
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'name'   => 'required',
            'enable' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {
            $category           = new Category;
            $category->name     = request()->name;
            $category->enable   = request()->enable;
            $category->save();

            return ResponseFormatter::success($category,'Store Category Success');

        } catch (\Throwable $th) {
            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            'name'          => 'required',
            'category_id'   => 'required|integer',
            'enable'        => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(null,$validator->errors()->first(), 500);
        }

        try {
            $category           = Category::find(request()->category_id);
            $category->name     = request()->name;
            $category->enable   = request()->enable;
            $category->save();

            return ResponseFormatter::success($category,'Category Successfully Updated');

        } catch (\Throwable $th) {
            return ResponseFormatter::error(null,$th->getMessage());
        }
    }

    public function destroy()
    {
        $category = Category::find(request()->category_id);

        if($category){
            CategoryProduct::where('category_id', request()->category_id)->get()->each(function ($item){
                $item->delete();
            });
            $category->delete();

            return ResponseFormatter::success($category,'Category Success Deleted');
        }else{
            return ResponseFormatter::error(null,'Category does not exist',500);
        }
    }
}
