<?php

namespace Tests\Feature;

use App\Http\Controllers\GrnController;
use App\Rules\UniqueGrnInvoiceNumber;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class GrnDuplicateInvoiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) $this->markTestSkipped('Requires pdo_sqlite.');
        Schema::create('grns', function (Blueprint $t) {
            $t->increments('id'); $t->integer('branch_id'); $t->string('supplier_invoice_number'); $t->softDeletes(); $t->timestamps();
        });
        Schema::create('inventories', function (Blueprint $t) {
            $t->increments('id'); $t->integer('grn_id'); $t->string('supplier_invoice_number'); $t->timestamps();
        });
        DB::table('grns')->insert([
            ['id' => 1, 'branch_id' => 2, 'supplier_invoice_number' => 'INV-001'],
            ['id' => 2, 'branch_id' => 2, 'supplier_invoice_number' => 'INV-002'],
        ]);
        DB::table('inventories')->insert(['grn_id' => 2, 'supplier_invoice_number' => 'INV-002']);
        $user = Mockery::mock();
        $user->shouldReceive('can')->andReturn(true);
        $user->shouldReceive('branchId')->andReturn(2);
        $user->shouldReceive('canEditGrnCostAndQuantity')->andReturn(true)->byDefault();
        Auth::shouldReceive('user')->andReturn($user);
    }

    public function test_create_redirects_reused_invoice_to_existing_grn_without_writing_another()
    {
        $request = Request::create('/grn/store', 'POST', [
            'vender_id' => 1, 'supplier_invoice_number' => ' inv-001 ', 'order_date' => '2026-11-05',
            'required_by' => '2026-11-05', 'items' => [['unit_cost' => 100]], 'status' => 3,
        ]);
        $controller = Mockery::mock(GrnController::class)->makePartial();
        $response = $controller->store($request);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('grnOrderEdit.OrderEdit', 1), $response->getTargetUrl());
        $this->assertStringContainsString('INV-001', session('info'));
        $this->assertStringContainsString('Add Item', session('info'));
        $this->assertEquals(2, DB::table('grns')->count());
    }

    public function test_invoice_correction_cannot_take_another_grns_number()
    {
        $controller = Mockery::mock(GrnController::class)->makePartial();
        try {
            $controller->updateInvoiceNumber(Request::create('/invoice', 'POST', ['supplier_invoice_number' => 'INV-001']), 2);
            $this->fail('Duplicate invoice correction was allowed.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('INV-001', $e->errors()['supplier_invoice_number'][0]);
            $this->assertSame('INV-002', DB::table('grns')->where('id', 2)->value('supplier_invoice_number'));
            $this->assertSame('INV-002', DB::table('inventories')->where('grn_id', 2)->value('supplier_invoice_number'));
        }
    }

    public function test_edit_can_keep_its_own_number_and_trim_whitespace()
    {
        $controller = Mockery::mock(GrnController::class)->makePartial();
        $response = $controller->updateInvoiceNumber(Request::create('/invoice', 'POST', ['supplier_invoice_number' => ' INV-002 ']), 2);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('INV-002', DB::table('grns')->where('id', 2)->value('supplier_invoice_number'));
    }

    public function test_deleted_invoice_numbers_can_be_reused_but_active_numbers_remain_unique()
    {
        $this->assertTrue((new UniqueGrnInvoiceNumber)->passes('supplier_invoice_number', 'NEW-003'));
        DB::table('grns')->where('id', 1)->update(['deleted_at' => '2026-11-05 12:00:00']);
        $this->assertTrue((new UniqueGrnInvoiceNumber)->passes('supplier_invoice_number', ' inv-001 '));
        $this->assertFalse((new UniqueGrnInvoiceNumber)->passes('supplier_invoice_number', ' inv-002 '));
        DB::table('grns')->insert(['id' => 3, 'branch_id' => 2, 'supplier_invoice_number' => 'INV-001']);
        $this->assertFalse((new UniqueGrnInvoiceNumber)->passes('supplier_invoice_number', 'INV-001'));
    }

    public function test_lookup_redirects_to_same_branch_existing_grn()
    {
        $controller = Mockery::mock(GrnController::class)->makePartial();
        $response = $controller->lookupInvoice(Request::create('/lookup', 'GET', ['supplier_invoice_number' => ' inv-001 ']));
        $this->assertSame(route('grnOrderEdit.OrderEdit', 1), $response->getData(true)['redirect']);
        $response = $controller->lookupInvoice(Request::create('/lookup', 'GET', ['supplier_invoice_number' => 'NEW']));
        $this->assertNull($response->getData(true)['redirect']);
    }

    public function test_lookup_does_not_expose_another_branchs_grn_or_allow_non_admin_additions()
    {
        $controller = Mockery::mock(GrnController::class)->makePartial();
        DB::table('grns')->where('id', 1)->update(['branch_id' => 99]);
        $response = $controller->lookupInvoice(Request::create('/lookup', 'GET', ['supplier_invoice_number' => 'INV-001']));
        $this->assertNull($response->getData(true)['redirect']);
        Auth::user()->shouldReceive('canEditGrnCostAndQuantity')->andReturn(false);
        $response = $controller->lookupInvoice(Request::create('/lookup', 'GET', ['supplier_invoice_number' => 'INV-002']));
        $this->assertNull($response->getData(true)['redirect']);
    }
}
