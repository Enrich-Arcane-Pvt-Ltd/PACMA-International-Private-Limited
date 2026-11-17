<?php

namespace Tests\Feature;

use App\Http\Controllers\GrnController;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class GrnEditAccessTest extends TestCase
{
    /** @dataProvider edits */
    public function test_protected_values_are_admin_only($type, $cost, $quantity, $denied, $productId = 10)
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('Requires pdo_sqlite.');
        }
        Schema::create('grns', function (Blueprint $table) {
            $table->increments('id'); $table->integer('branch_id'); $table->string('supplier_invoice_number')->nullable(); $table->softDeletes();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
        });
        DB::table('products')->insert([['id' => 10], ['id' => 11]]);
        Schema::create('grn_products', function (Blueprint $table) {
            $table->increments('id'); $table->integer('grn_id'); $table->integer('product_id');
            $table->decimal('unit_cost', 12, 2); $table->integer('quantity'); $table->integer('received_qty')->default(3); $table->softDeletes();
        });
        Schema::create('product_warranty_serials', function (Blueprint $table) {
            $table->increments('id'); $table->integer('grn_product_id');
        });
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id'); $table->integer('grn_id');
            $table->integer('grn_qty'); $table->integer('remain_qty');
        });
        DB::table('grns')->insert(['id' => 1, 'branch_id' => 2]);
        DB::table('grn_products')->insert(['id' => 5, 'grn_id' => 1, 'product_id' => 10, 'unit_cost' => 100, 'quantity' => 3]);
        // Stop permitted requests at the existing sold-stock guard before any writes.
        DB::table('inventories')->insert(['grn_id' => 1, 'grn_qty' => 3, 'remain_qty' => 2]);
        $user = Mockery::mock(User::class)->makePartial();
        $user->type = $type;
        $user->shouldReceive('can')->with('edit grn')->andReturn(true);
        $user->shouldReceive('branchId')->andReturn(2);
        Auth::shouldReceive('user')->andReturn($user);
        if ($user->canEditGrnCostAndQuantity()) {
            $service = Mockery::mock(\App\Services\GrnReceiptAdditions::class);
            $service->shouldReceive('save')->once();
            $this->app->instance(\App\Services\GrnReceiptAdditions::class, $service);
        }
        $controller = Mockery::mock(GrnController::class)->makePartial();
        $request = Request::create('/grn/update/1', 'POST', [
            'vender_id' => 1, 'supplier_invoice_number' => 'INV',
            'items' => [['id' => 5, 'item' => $productId, 'unit_cost' => $cost, 'quantity' => $quantity,
                'receive_quantity' => $quantity, 'grn_cost' => 100, 'sale_price' => 120, 'hire_price' => 166]],
        ]);
        if ($denied) {
            $this->expectException(HttpException::class);
            $this->expectExceptionMessage('Only administrators');
        }
        $response = $controller->update($request, 1);
        if (!$denied) {
            $this->assertSame(302, $response->getStatusCode());
            if ($user->canEditGrnCostAndQuantity()) {
                $this->assertStringContainsString('invoiced units were preserved', session('success'));
            } else {
                $this->assertStringContainsString('already been sold', session('error'));
            }
        }
    }

    public function edits()
    {
        return [
            ['staff', 101, 3, true], ['staff', 100, 4, true],
            ['staff', '100.00', 3, false], ['Admin', 101, 4, false],
            ['super admin', 101, 4, false], ['company', 101, 4, false],
            ['staff', 100, 3, false, 11], ['Admin', 101, 4, false, 11],
        ];
    }
}
