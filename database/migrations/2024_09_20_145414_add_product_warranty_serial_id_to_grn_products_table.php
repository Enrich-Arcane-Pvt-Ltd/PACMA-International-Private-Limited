<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductWarrantySerialIdToGrnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grn_products', function (Blueprint $table) {
            $table->foreignId('product_warranty_serial_id')->nullable()->constrained('product_warranty_serials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grn_products', function (Blueprint $table) {
            $table->dropForeign(['product_warranty_serial_id']);
            $table->dropColumn('product_warranty_serial_id');
        });
    }
}
