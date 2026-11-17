<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create(
            'products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_id');
            $table->string('name');
            $table->string('sku');
            $table->float('sale_price',10,2);
            $table->float('purchase_price',10,2);
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->string('warranty_period');
            $table->longText('warranty_document');
            $table->longText('image_1');
            $table->longText('image_2');
            $table->longText('image_3');
            $table->integer('status');
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->integer('created_by')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
