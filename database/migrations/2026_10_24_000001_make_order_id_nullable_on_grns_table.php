<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeOrderIdNullableOnGrnsTable extends Migration
{
    public function up()
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->integer('order_id')->nullable()->change();
        });
    }

    public function down()
    {
        // Manual GRNs have no purchase order; do not invent one during rollback.
        if (DB::table('grns')->whereNull('order_id')->exists()) {
            throw new RuntimeException('Cannot make grns.order_id required while manual GRNs exist.');
        }

        Schema::table('grns', function (Blueprint $table) {
            $table->integer('order_id')->nullable(false)->change();
        });
    }
}
