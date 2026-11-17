<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkInventoryToGrnProduct extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('inventories', 'grn_product_id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->unsignedBigInteger('grn_product_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['grn_product_id']);
            $table->dropColumn('grn_product_id');
        });
    }
}
