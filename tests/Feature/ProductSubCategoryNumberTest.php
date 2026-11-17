<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductSubCategoryController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductSubCategoryNumberTest extends TestCase
{
    public function test_number_generation_handles_legacy_codes_and_uses_highest_number()
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('Requires pdo_sqlite.');
        }
        Schema::create('product_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_sub_category_id')->nullable();
        });
        $controller = new ProductSubCategoryController;
        $this->assertSame(1, $controller->productSubCategoryNumber());
        foreach (['21', 'SUB00035', '8', 'Furniture', null] as $number) {
            DB::table('product_sub_categories')->insert(['product_sub_category_id' => $number]);
        }
        $this->assertSame(36, $controller->productSubCategoryNumber());
    }
}
