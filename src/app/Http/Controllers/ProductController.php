<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $products = Product::select(
                'products.id', 'products.name', 'products.description', 'products.price', 'products.quantity', 'products.active', 'products.created_at', 'products.updated_at',
                DB::raw('GROUP_CONCAT(categories.name SEPARATOR ", ") as categories')
            )
            ->Join('category_product', 'products.id', '=', 'category_product.product_id')
            ->Join('categories', 'category_product.category_id', '=', 'categories.id')
            ->groupBy('products.id') // Group by the product's primary key
            ->active()
            ->latest()
            ->get();

       return response(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->validated();
                
                $product = Product::create($data['product']);
                
                $file = $request->file('image');
                if($file){
                    $file_name = $file->hashName();
                    
                    $product->storeAs('product/images', $file_name, 'public');
        
                    $product->image = $file_name;
        
                    $product->save();
                }
    
                $product->categories()->attach($data['categories']);
             
                return response(['product' => $product], 201);
            });
    
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to save product.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            return DB::transaction(function () use($product) {
                if($product->image){
                    $asset =  "storage/product/images/".$product->image;
                    
                    Storage::disk('public')->delete($asset);
                }
            
                $product->categories()->detach();
                $product->delete();
                return response(['message' => 'Product deleted successfully!']); 
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Failed to delete product.'], 500);
        }
    }
}
