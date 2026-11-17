<?php

namespace Tests\Feature;

use App\Grn;
use App\Http\Controllers\GrnController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class GrnInvoiceNumberTest extends TestCase
{
    public function test_invoice_correction_preserves_sold_stock_and_other_grns()
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('Enable pdo_sqlite to run the isolated invoice update test.');
        }
        Schema::create('grns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('supplier_invoice_number');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grn_id');
            $table->integer('grn_qty');
            $table->integer('remain_qty');
            $table->string('supplier_invoice_number');
            $table->timestamps();
        });
        DB::table('grns')->insert([
            ['id' => 1, 'branch_id' => 2, 'supplier_invoice_number' => 'OLD'],
            ['id' => 2, 'branch_id' => 2, 'supplier_invoice_number' => 'OTHER'],
        ]);
        DB::table('inventories')->insert([
            ['id' => 1, 'grn_id' => 1, 'grn_qty' => 5, 'remain_qty' => 2, 'supplier_invoice_number' => 'OLD'],
            ['id' => 2, 'grn_id' => 2, 'grn_qty' => 3, 'remain_qty' => 3, 'supplier_invoice_number' => 'OTHER'],
        ]);
        $user = Mockery::mock();
        $user->shouldReceive('can')->with('edit grn')->andReturn(true);
        $user->shouldReceive('branchId')->andReturn(2);
        Auth::shouldReceive('user')->andReturn($user);
        $controller = Mockery::mock(GrnController::class)->makePartial();
        $request = Request::create('/invoice-number', 'POST', ['supplier_invoice_number' => 'NEW-001']);
        $response = $controller->updateInvoiceNumber($request, 1);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('NEW-001', Grn::find(1)->supplier_invoice_number);
        $stock = DB::table('inventories')->where('id', 1)->first();
        $this->assertSame('NEW-001', $stock->supplier_invoice_number);
        $this->assertEquals(2, $stock->remain_qty);
        $this->assertEquals(5, $stock->grn_qty);
        $this->assertSame('OTHER', DB::table('inventories')->where('id', 2)->value('supplier_invoice_number'));
    }
}
