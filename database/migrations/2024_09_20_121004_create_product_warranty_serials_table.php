<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWarrantySerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_warranty_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_product_id')->nullable()->constrained('grn_products');
            $table->string('serial_number')->nullable();
            $table->string('warranty_number')->nullable();
            $table->tinyInteger('status')->default(0)->comment("0 - Available , 1 - Used , 2 - Not Available");
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
        Schema::dropIfExists('product_warranty_serials');
    }
}
