<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\category;
use App\Models\product;
use App\Models\subCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class subCategoryController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->only('category_id', 'subCategoryName');
        $validator = Validator::make($data, [
            'category_id' => 'required|string',
            'subCategoryName' => 'required',

        ]);

        $checkCategory  = category::find($request->category_id);
        if (!$checkCategory) {
            return ResponseFormatter::error('data category tidak ada');
        };
        if ($validator->fails()) {
            return
                ResponseFormatter::error($validator->errors(), 'Failed');
        }

        $subCategoryData = subCategory::create([
            'category_id' => $request->category_id,
            'subCategoryName' => $request->subCategoryName
        ]);

        return ResponseFormatter::success($subCategoryData, 'Sub Category Berhasil ditambahkan');
    }

    public function get()
    {
        $subCategory = subCategory::with('category')->get();
        return ResponseFormatter::success($subCategory, 'Sub Category Berhasil ditambahkan');
    }
    public function destroy($id)
    {

        $categoryData = subCategory::where('id',$id)->first();
        $product = product::where('subcategory_id',$categoryData->id)->get();
        foreach($product as $product){
            $product->delete();
        }
        if (!$categoryData) {
            ResponseFormatter::error(null, 'Subcategory tidak tersedia');
        }

        $categoryData->delete();
        return ResponseFormatter::success(null, 'Success');
    }


    public function update(Request $request, $id)
    {
        $categoryData = subCategory::where('id', $id)->first();

        if ($categoryData === null) {
            ResponseFormatter::error(null, 'Subcategory tidak tersedia');
        }


        $categoryData->category_id = $request->category_id;
        $categoryData->subCategoryName = $request->subCategoryName;
        $categoryData->save();
        return ResponseFormatter::success($categoryData, 'Sub Category Berhasil diupdate');
    }
}
