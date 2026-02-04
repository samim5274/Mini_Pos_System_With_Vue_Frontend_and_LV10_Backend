<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

    private function generateSku(string $name): string
    {
        $base = strtoupper(Str::slug($name, ''));
        $base = substr($base, 0, 8);
        $sku  = $base . '-' . rand(1000, 9999);

        while (Product::where('sku', $sku)->exists()) {
            $sku = $base . '-' . rand(1000, 9999);
        }
        return $sku;
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
            'sku'            => 'nullable|string|max:100|unique:products,sku',
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

        $data = $validator->validated();

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

        // SKU auto-generate if empty
        if (empty($data['sku'])) {
            $sku = $this->generateSku($data['name']); // must return unique
        }

        $product = Product::create([
            'name'           => $request->name,
            'sku'            => $sku,
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

    public function delete($id) {
        try {
            $product = Product::findOrFail($id);

            DB::transaction(function () use ($product) {

                // delete image from storage
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                // delete product
                $product->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id){
        try {
            $product = Product::findOrFail($id);

            $product->image_url = $product->image ? asset('storage/'.$product->image) : null;

            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id){
        
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:100|unique:products,sku,' . $product->id, // ✅ exclude current
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

        try {
            DB::transaction(function () use ($request, $product) {

                // if new image uploaded, delete old image + store new
                if ($request->hasFile('image')) {

                    // delete old file
                    if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }

                    // upload new file
                    $newPath = $this->uploadPhoto($request->file('image'), 'products');

                    if (!$newPath) {
                        // throw to rollback
                        throw new \Exception("Image size must not exceed 2MB.");
                    }

                    $product->image = $newPath;
                }

                // update other fields
                $product->name           = $request->name;
                $product->sku            = $request->sku;
                $product->price          = $request->price;
                $product->stock_quantity = $request->stock_quantity;
                $product->min_stock      = $request->min_stock;

                $product->save();
            });

            // attach image url for frontend
            $product->refresh();
            $product->image_url = $product->image ? asset('storage/'.$product->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data'    => $product
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
