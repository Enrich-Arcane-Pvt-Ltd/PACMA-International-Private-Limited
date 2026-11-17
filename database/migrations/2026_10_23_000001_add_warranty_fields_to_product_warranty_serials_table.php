<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarrantyFieldsToProductWarrantySerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_warranty_serials', function (Blueprint $table) {
            if (!Schema::hasColumn('product_warranty_serials', 'warranty_period')) {
                $table->integer('warranty_period')->nullable()->after('warranty_number');
            }
            if (!Schema::hasColumn('product_warranty_serials', 'warranty_document')) {
                $table->string('warranty_document')->nullable()->after('warranty_period');
            }
            if (!Schema::hasColumn('product_warranty_serials', 'additional_warranty_description')) {
                $table->text('additional_warranty_description')->nullable()->after('warranty_document');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_warranty_serials', function (Blueprint $table) {
            $table->dropColumn(['warranty_period', 'warranty_document', 'additional_warranty_description']);
        });
    }
}
