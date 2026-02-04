<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Product;

class ProductController extends Controller
{
    public function products(){

        $products = Product::latest()->paginate(10);

        $products->getCollection()->transform(function ($p) {
            $p->image_url = $p->image ? asset('storage/' . $p->image) : null;
            return $p;
        });

        return response()->json($products);
    }

    private function uploadPhoto($file, $folder)
    {
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file->getSize() > $maxSize) {
            return false; // controller handle message
        }

        $name = 'pdr_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        // storage/app/public/products
        return $file->storeAs($folder, $name, 'public');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:100|unique:products,sku',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $this->uploadPhoto($request->file('image'), 'products');

            if (!$imagePath) {                    
                return response()->json([
                    'success' => false,
                    'message' => "Image size must not exceed 2MB.",
                ], 422);
            }

            // $validated['image'] = $imagePath;
        }

        $product = Product::create([
            'name'           => $request->name,
            'sku'            => $request->sku,
            'price'          => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'min_stock'      => $request->min_stock,
            'image'          => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }
}
