<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('name',100);
            $table->string('image',255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price',10, 2);
            $table->integer('quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('shop_id');
            $table->index('category_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
