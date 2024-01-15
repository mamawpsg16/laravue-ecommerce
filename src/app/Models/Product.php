<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $appends = ['product_image'];

    protected function productImage(): Attribute
    {
        $asset =  $this->image ? "/product/images/".$this->image : "/default_images/product.png";
        return new Attribute(
            get: fn () => asset("storage".$asset),
        );
    }
    
    public function categories(){
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
}
