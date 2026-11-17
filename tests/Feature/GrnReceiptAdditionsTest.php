<?php

namespace Tests\Feature;

use App\Services\GrnReceiptAdditions;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class GrnReceiptAdditionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) $this->markTestSkipped('Requires pdo_sqlite.');
        Schema::create('grns', function (Blueprint $t) {
            $t->increments('id'); $t->integer('branch_id'); $t->integer('vender_id');
            $t->string('order_date'); $t->string('required_by'); $t->string('supplier_invoice_number');
            $t->decimal('total_amount', 12, 2); $t->softDeletes(); $t->timestamps();
        });
        Schema::create('grn_products', function (Blueprint $t) {
            $t->increments('id'); $t->integer('grn_id'); $t->integer('product_id');
            $t->integer('quantity'); $t->integer('received_qty');
            foreach (['unit_cost', 'grn_cost', 'sale_price', 'hire_price', 'amount'] as $c) $t->decimal($c, 12, 2);
            $t->softDeletes(); $t->timestamps();
        });
        Schema::create('inventories', function (Blueprint $t) {
            $t->increments('id'); $t->integer('grn_id'); $t->integer('product_id'); $t->integer('branch_id');
            $t->unsignedBigInteger('grn_product_id')->nullable();
            $t->integer('grn_qty'); $t->integer('remain_qty'); $t->string('date');
            foreach (['purchase_price', 'sale_price', 'hire_price'] as $c) $t->decimal($c, 12, 2);
            $t->string('supplier_invoice_number'); $t->integer('status'); $t->timestamps();
        });
        Schema::create('product_warranty_serials', function (Blueprint $t) {
            $t->increments('id'); $t->integer('grn_product_id'); $t->string('serial_number');
            $t->string('warranty_number')->nullable(); $t->integer('warranty_period')->nullable();
            $t->string('warranty_document')->nullable(); $t->text('additional_warranty_description')->nullable();
            $t->integer('status'); $t->timestamps();
        });
        Schema::create('stock_transactions', function (Blueprint $t) {
            $t->increments('id');
            foreach (['stock_master_id', 'inventory_id', 'product_id', 'quantity', 'branch_id', 'created_by', 'status'] as $c) $t->integer($c);
            $t->string('date'); $t->timestamps();
        });
        Schema::create('account_transactions', function (Blueprint $t) {
            $t->increments('id');
            foreach (['chart_of_account_id', 'related_chart_of_account_id', 'transaction_type_id', 'branch_id', 'grn_id', 'product_id', 'vender_id', 'status', 'debit_credit', 'created_by'] as $c) $t->integer($c);
            $t->decimal('amount', 12, 2); $t->string('date'); $t->timestamps();
        });
        Schema::create('products', function (Blueprint $t) { $t->increments('id'); $t->integer('product_category_id'); });
        Schema::create('product_categories', function (Blueprint $t) { $t->increments('id'); $t->integer('inventory_chart_of_account_id'); $t->string('name')->default('Electronics'); });
        DB::table('products')->insert([['id' => 10, 'product_category_id' => 1], ['id' => 11, 'product_category_id' => 1]]);
        DB::table('product_categories')->insert(['id' => 1, 'inventory_chart_of_account_id' => 5]);
        DB::table('grns')->insert(['id' => 1, 'branch_id' => 2, 'vender_id' => 3, 'order_date' => '2026-11-05', 'required_by' => '2026-11-05', 'supplier_invoice_number' => 'INV', 'total_amount' => 200]);
        DB::table('grn_products')->insert(['id' => 5, 'grn_id' => 1, 'product_id' => 10, 'quantity' => 2, 'received_qty' => 2, 'unit_cost' => 100, 'grn_cost' => 100, 'sale_price' => 120, 'hire_price' => 166, 'amount' => 200]);
        DB::table('inventories')->insert(['id' => 7, 'grn_id' => 1, 'product_id' => 10, 'branch_id' => 2, 'grn_qty' => 2, 'remain_qty' => 1, 'date' => '2026-11-05', 'purchase_price' => 100, 'sale_price' => 120, 'hire_price' => 166, 'supplier_invoice_number' => 'INV', 'status' => 1]);
        DB::table('product_warranty_serials')->insert([
            ['id' => 20, 'grn_product_id' => 5, 'serial_number' => 'SOLD', 'warranty_number' => 'W1', 'status' => 1],
            ['id' => 21, 'grn_product_id' => 5, 'serial_number' => 'AVAILABLE', 'warranty_number' => 'W2', 'status' => 0],
        ]);
        DB::table('stock_transactions')->insert(['id' => 9, 'stock_master_id' => 1, 'inventory_id' => 7, 'product_id' => 10, 'quantity' => 2, 'date' => '2026-11-05', 'branch_id' => 2, 'created_by' => 2, 'status' => 1]);
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 2; $user->type = 'Admin';
        $user->shouldReceive('branchId')->andReturn(2);
        $user->shouldReceive('can')->with('edit grn')->andReturn(true);
        $this->actingAs($user);
    }

    private function saleFixture()
    {
        Schema::create('sales', function (Blueprint $t) {
            $t->increments('id');
            foreach (['branch_id', 'inventory_id', 'product_id', 'quantity', 'modal_type', 'sale_type', 'status', 'invoice_id', 'created_by'] as $column) $t->integer($column)->default(0);
            $t->decimal('purchase_price', 12, 2)->default(100); $t->decimal('unit_price', 12, 2)->default(120);
            $t->string('date_of_sale'); $t->timestamps();
        });
        Schema::create('sale_items', function (Blueprint $t) {
            $t->increments('id');
            foreach (['sale_id', 'inventory_id', 'product_id', 'quantity', 'product_warranty_id', 'status', 'invoice_id'] as $column) $t->integer($column)->default(0);
            $t->decimal('unit_price', 12, 2)->default(120); $t->string('date_of_sale'); $t->timestamps();
        });
        Schema::create('hire_purchase_details', function (Blueprint $t) { $t->increments('id'); $t->integer('sale_id'); $t->integer('product_warranty_id'); });
        DB::table('sales')->insert(['id' => 1, 'branch_id' => 2, 'inventory_id' => 7, 'product_id' => 10, 'quantity' => 1,
            'modal_type' => 1, 'sale_type' => 1, 'status' => 2, 'date_of_sale' => '2026-11-05', 'invoice_id' => 1, 'created_by' => 2]);
        DB::table('hire_purchase_details')->insert(['sale_id' => 1, 'product_warranty_id' => 20]);
        DB::table('stock_transactions')->insert(['id' => 10, 'stock_master_id' => 2, 'inventory_id' => 7, 'product_id' => 10,
            'quantity' => 1, 'date' => '2026-11-05', 'branch_id' => 2, 'created_by' => 2, 'status' => 1]);
        return \App\Sale::find(1);
    }

    public function test_batch_catalog_separates_prices_and_includes_only_own_sold_serial_during_edit()
    {
        $sale = $this->saleFixture();
        $batches = \App\Services\SaleBatchCatalog::list(10);
        $this->assertEquals(1, $batches[0]['available_qty']);
        $this->assertCount(1, $batches[0]['units']);
        $this->assertEquals('AVAILABLE', $batches[0]['units'][0]['serial_number']);
        $edit = \App\Services\SaleBatchCatalog::list(10, $sale);
        $this->assertEquals(2, $edit[0]['available_qty']);
        $this->assertCount(2, $edit[0]['units']);
        DB::table('inventories')->insert(['id' => 8, 'grn_id' => 1, 'product_id' => 10, 'branch_id' => 2,
            'grn_product_id' => 6, 'grn_qty' => 3, 'remain_qty' => 3, 'date' => '2026-11-06', 'purchase_price' => 50,
            'sale_price' => 70, 'hire_price' => 97, 'supplier_invoice_number' => 'INV', 'status' => 1]);
        $batches = \App\Services\SaleBatchCatalog::list(10);
        $this->assertEquals(4, $batches->sum('available_qty'));
        $this->assertEquals(100, $batches[0]['purchase_price']);
        $this->assertEquals(50, $batches[1]['purchase_price']);
        auth()->user()->shouldReceive('can')->with('manage stock')->andReturn(true);
        $html = view('sale.batch-controls', ['batchCash' => false, 'sales' => $sale])->render();
        $this->assertStringContainsString('SaleBatches.install', $html);
        $this->assertRegExp('/"inventory_id":"?7"?/', $html);
    }

    public function test_batch_selection_is_required_even_if_inventory_id_is_posted()
    {
        $this->expectException(ValidationException::class);
        \App\Services\SaleStockBatch::prepare(['product' => 10, 'inventory_id' => 7, 'quantity' => 1]);
    }

    public function test_sale_edit_reallocates_only_selected_batch()
    {
        $sale = $this->saleFixture();
        $request = Request::create('/sale/update', 'POST', ['product' => 10, 'batch_id' => 7,
            'quantity' => 2, 'serial_card_no' => 'SOLD', 'product_warranty_serial_id' => 20, 'date_of_sale' => '2026-11-05']);
        DB::transaction(function () use ($request, $sale) { \App\Services\SaleBatchEdit::prepare($request, $sale); });
        $this->assertEquals(0, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(0, DB::table('stock_transactions')->where('id', 10)->value('status'));
        $this->assertEquals(2, DB::table('stock_transactions')->where('stock_master_id', 2)->where('status', 1)->sum('quantity'));
    }

    public function test_sale_edit_ambiguous_legacy_movement_rolls_back_stock_and_serial_release()
    {
        $sale = $this->saleFixture();
        DB::table('stock_transactions')->insert(['stock_master_id' => 2, 'inventory_id' => 7, 'product_id' => 10,
            'quantity' => 1, 'date' => '2026-11-05', 'branch_id' => 2, 'created_by' => 2, 'status' => 1]);
        $request = Request::create('/sale/update', 'POST', ['product' => 10, 'batch_id' => 7,
            'quantity' => 2, 'serial_card_no' => 'SOLD', 'product_warranty_serial_id' => 20, 'date_of_sale' => '2026-11-05']);
        try {
            DB::transaction(function () use ($request, $sale) { \App\Services\SaleBatchEdit::prepare($request, $sale); });
            $this->fail('Ambiguous historical allocation was guessed.');
        } catch (ValidationException $error) {
            $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('remain_qty'));
            $this->assertEquals(1, DB::table('product_warranty_serials')->where('id', 20)->value('status'));
            $this->assertEquals(2, DB::table('stock_transactions')->where('stock_master_id', 2)->where('status', 1)->count());
        }
    }

    public function test_batch_report_filters_movements_and_sales_without_changing_current_balance()
    {
        $this->saleFixture();
        auth()->user()->shouldReceive('can')->with('manage stock')->andReturn(true);
        Schema::create('branches', function (Blueprint $t) { $t->increments('id'); $t->string('name'); });
        Schema::create('invoices', function (Blueprint $t) { $t->increments('id'); $t->string('invoice_code'); });
        DB::table('invoices')->insert([['id' => 1, 'invoice_code' => 'H000001'], ['id' => 2, 'invoice_code' => 'R000002']]);
        DB::table('branches')->insert(['id' => 2, 'name' => 'Test Branch']);
        DB::table('sales')->insert(['id' => 2, 'branch_id' => 2, 'modal_type' => 2, 'sale_type' => 0,
            'status' => 1, 'date_of_sale' => '2026-11-06', 'invoice_id' => 2]);
        DB::table('sale_items')->insert(['sale_id' => 2, 'inventory_id' => 7, 'product_id' => 10,
            'quantity' => 1, 'product_warranty_id' => 21, 'status' => 1, 'invoice_id' => 2, 'date_of_sale' => '2026-11-06']);
        $controller = new \App\Http\Controllers\StockBatchController;
        $data = $controller->show(Request::create('/stock/batch/7', 'GET', ['from' => '2026-11-06', 'to' => '2026-11-06']), 7)->getData();
        $this->assertCount(1, $data['cashRows']);
        $this->assertCount(0, $data['singleRows']);
        $this->assertEquals(1, $data['batch']->remain_qty);
        $this->assertCount(0, $data['movements']);
        $this->assertCount(2, $data['units']);
        $data = $controller->show(Request::create('/stock/batch/7', 'GET'), 7)->getData();
        $this->assertCount(1, $data['singleRows']);
        $this->assertCount(2, $data['movements']);
    }

    public function test_cash_sale_cancellation_returns_only_active_rows_and_leaves_archived_rows_alone()
    {
        $sale = $this->saleFixture();
        $sale->modal_type = 2; $sale->sale_type = 0; $sale->save();
        foreach ([1, 0] as $status) {
            DB::table('sale_items')->insert(['sale_id' => 1, 'inventory_id' => 7, 'product_id' => 10,
                'quantity' => 1, 'product_warranty_id' => 20, 'status' => $status, 'date_of_sale' => '2026-11-05']);
        }
        $request = Request::create('/cancel', 'POST', ['date_of_sale' => '2026-11-05']);
        DB::transaction(function () use ($request, $sale) { \App\Services\SaleBatchEdit::prepare($request, $sale, true); });
        $this->assertEquals(2, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(0, DB::table('stock_transactions')->where('id', 10)->value('status'));
        $this->assertEquals(0, DB::table('product_warranty_serials')->where('id', 20)->value('status'));
    }

    public function test_a_serial_from_another_batch_cannot_override_selected_batch()
    {
        DB::table('inventories')->insert(['id' => 8, 'grn_id' => 1, 'product_id' => 10, 'branch_id' => 2,
            'grn_product_id' => 6, 'grn_qty' => 3, 'remain_qty' => 3, 'date' => '2026-11-06', 'purchase_price' => 50,
            'sale_price' => 70, 'hire_price' => 97, 'supplier_invoice_number' => 'INV', 'status' => 1]);
        DB::table('inventories')->where('id', 7)->update(['grn_product_id' => 5]);
        $this->expectException(ValidationException::class);
        \App\Services\SaleStockBatch::prepare(['product' => 10, 'batch_id' => 8, 'quantity' => 1,
            'serial_card_no' => 'AVAILABLE', 'product_warranty_serial_id' => 21]);
    }

    public function test_batch_options_endpoint_excludes_other_branches_and_requires_owned_sale_for_edit()
    {
        $this->saleFixture();
        auth()->user()->shouldReceive('can')->with('create sale')->andReturn(true);
        auth()->user()->shouldReceive('can')->with('edit sale')->andReturn(true);
        $controller = new \App\Http\Controllers\StockBatchController;
        $response = $controller->options(Request::create('/batches', 'GET'), 10)->getData(true);
        $this->assertEquals(1, $response['batches'][0]['available_qty']);
        $this->assertCount(1, $response['batches'][0]['units']);
        DB::table('sales')->where('id', 1)->update(['branch_id' => 99]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $controller->options(Request::create('/batches', 'GET', ['sale_id' => 1]), 10);
    }

    private function receiptRequest()
    {
        return Request::create('/grn/update/1', 'POST', [
            'supplier_invoice_number' => 'CORRECTED',
            'items' => [['id' => 5, 'item' => 10, 'quantity' => 3, 'receive_quantity' => 3, 'unit_cost' => 100, 'grn_cost' => 100, 'sale_price' => 120, 'hire_price' => 166]],
            'serial_warranty_data' => json_encode(['product_10' => [
                ['id' => 20, 'serial' => 'SOLD', 'warranty' => 'W1'],
                ['id' => 21, 'serial' => 'AVAILABLE', 'warranty' => 'W2'],
                ['serial' => 'NEW', 'warranty' => 'W3', 'warranty_period' => 12],
            ]]),
        ]);
    }

    public function test_same_product_can_have_two_prices_in_one_grn_with_separate_stock_and_warranties()
    {
        $request = $this->receiptRequest();
        $items = $request->items;
        $items[0]['quantity'] = $items[0]['receive_quantity'] = 2;
        $items[] = ['item' => 10, 'batch_key' => 'new_second', 'quantity' => 1, 'receive_quantity' => 1,
            'unit_cost' => 50, 'grn_cost' => 50, 'sale_price' => 70, 'hire_price' => 97];
        $units = json_decode($request->serial_warranty_data, true);
        array_pop($units['product_10']);
        $units['new_second'] = [['serial' => 'CHEAPER', 'warranty' => 'SECOND-CARD', 'warranty_period' => 6]];
        $request->merge(['items' => $items, 'serial_warranty_data' => json_encode($units)]);
        (new GrnReceiptAdditions)->save($request, 1);
        $this->assertEquals(2, DB::table('inventories')->count());
        $this->assertEquals(100, DB::table('inventories')->where('id', 7)->value('purchase_price'));
        $second = DB::table('inventories')->where('id', '!=', 7)->first();
        $this->assertEquals(50, $second->purchase_price);
        $this->assertEquals(1, $second->remain_qty);
        $this->assertNotEquals(5, $second->grn_product_id);
        $this->assertEquals($second->grn_product_id, DB::table('product_warranty_serials')->where('serial_number', 'CHEAPER')->value('grn_product_id'));
        $this->assertEquals(250, DB::table('grns')->where('id', 1)->value('total_amount'));
        $this->assertEquals(50, DB::table('account_transactions')->where('debit_credit', 1)->sum('amount'));

        $saleItem = \App\Services\SaleStockBatch::prepare(['product' => 10, 'inventory_id' => $second->id, 'batch_id' => $second->id,
            'quantity' => 1, 'serial_card_no' => 'CHEAPER']);
        $this->assertEquals($second->id, $saleItem['inventory_id']);
        $this->assertEquals(50, $saleItem['purchase_price']);

        // A subsequent edit must resolve each row to its own batch, regardless of row order.
        $items[1]['id'] = $second->grn_product_id;
        $request->merge(['items' => array_reverse($items)]);
        (new GrnReceiptAdditions)->save($request, 1);
        $this->assertEquals(2, DB::table('inventories')->count());
        $this->assertEquals(100, DB::table('inventories')->where('id', 7)->value('purchase_price'));
        $this->assertEquals(50, DB::table('inventories')->where('id', $second->id)->value('purchase_price'));
        $html = view('stock.quantity', ['branch' => (object) ['name' => 'Test Branch'],
            'current_inventory' => \App\Inventory::orderBy('id')->get(), 'last_purchase' => null, 'currencySymbol' => 'Rs.'])->render();
        $this->assertStringContainsString('100.00', $html);
        $this->assertStringContainsString('50.00', $html);
        $this->assertStringContainsString('B' . str_pad($second->id, 6, '0', STR_PAD_LEFT), $html);
    }

    public function test_sale_cannot_take_more_stock_than_the_selected_batch_or_use_another_branch()
    {
        try {
            \App\Services\SaleStockBatch::prepare(['product' => 10, 'inventory_id' => 7, 'batch_id' => 7, 'quantity' => 2, 'serial_card_no' => 'AVAILABLE']);
            $this->fail('Sale exceeded the batch quantity.');
        } catch (ValidationException $error) {
            $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        }
        DB::table('grns')->where('id', 1)->update(['branch_id' => 99]);
        $this->expectException(ValidationException::class);
        \App\Services\SaleStockBatch::prepare(['product' => 10, 'inventory_id' => 7, 'batch_id' => 7, 'quantity' => 1, 'serial_card_no' => 'AVAILABLE']);
    }

    public function test_replenishing_a_fully_sold_batch_reactivates_only_the_added_quantity()
    {
        DB::table('inventories')->where('id', 7)->update(['remain_qty' => 0, 'status' => 0]);
        DB::table('product_warranty_serials')->update(['status' => 1]);
        (new GrnReceiptAdditions)->save($this->receiptRequest(), 1);
        $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('status'));
        $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(2, DB::table('product_warranty_serials')->where('status', 1)->count());
    }

    public function test_sale_warranty_lookup_returns_received_unit_details_and_excludes_sold_units()
    {
        DB::table('product_warranty_serials')->where('id', 21)->update([
            'warranty_period' => 24,
            'warranty_document' => 'assets/documents/warranty/card.pdf',
            'additional_warranty_description' => 'Manufacturer terms',
        ]);
        $controller = (new \ReflectionClass(\App\Http\Controllers\SaleController::class))->newInstanceWithoutConstructor();
        $data = $controller->getWarrantySerials(10)->getData(true);
        $this->assertCount(1, $data);
        $this->assertEquals('AVAILABLE', $data[0]['serial_number']);
        $this->assertEquals(24, $data[0]['warranty_period']);
        $this->assertEquals('Manufacturer terms', $data[0]['additional_warranty_description']);
        $this->assertEquals('assets/documents/warranty/card.pdf', $data[0]['warranty_document']);
        DB::table('grns')->where('id', 1)->update(['branch_id' => 99]);
        $this->assertEquals(404, $controller->getWarrantySerials(10)->getStatusCode());
    }

    public function test_delete_without_stock_transaction_preserves_history_and_deactivates_inventory()
    {
        auth()->user()->shouldReceive('can')->with('delete grn')->andReturn(true);
        DB::table('inventories')->where('id', 7)->update(['remain_qty' => 2]);
        DB::table('product_warranty_serials')->update(['status' => 0]);
        DB::table('stock_transactions')->delete();
        (new \App\Services\GrnDeletion)->delete(1);
        $this->assertNotNull(DB::table('grns')->where('id', 1)->value('deleted_at'));
        $this->assertEquals(0, DB::table('inventories')->where('id', 7)->value('status'));
        $this->assertEquals(0, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(2, DB::table('product_warranty_serials')->where('status', 2)->count());
    }

    public function test_delete_of_sold_stock_is_rejected_without_changes()
    {
        auth()->user()->shouldReceive('can')->with('delete grn')->andReturn(true);
        try {
            (new \App\Services\GrnDeletion)->delete(1);
            $this->fail('Sold stock was deleted.');
        } catch (ValidationException $error) {
            $this->assertNull(DB::table('grns')->where('id', 1)->value('deleted_at'));
            $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('status'));
            $this->assertEquals(1, DB::table('stock_transactions')->where('id', 9)->value('status'));
        }
    }

    public function test_header_date_changes_update_receipt_and_ledger_metadata_without_duplicate_postings()
    {
        $request = $this->receiptRequest();
        $service = new GrnReceiptAdditions;
        $service->save($request, 1);
        DB::table('inventories')->where('id', 7)->update(['remain_qty' => 3]);
        DB::table('product_warranty_serials')->update(['status' => 0]);
        $request->merge(['vender_id' => 4, 'required_by' => '2026-11-20', 'order_date' => '2026-11-10']);
        $service->save($request, 1);
        $this->assertEquals(2, DB::table('account_transactions')->count());
        $this->assertEquals(2, DB::table('account_transactions')->where('vender_id', 4)->where('date', '2026-11-20')->count());
        $this->assertEquals('2026-11-10', DB::table('stock_transactions')->where('id', 9)->value('date'));
    }

    public function test_added_units_preserve_sold_ids_and_increment_stock_and_accounting_only_once()
    {
        (new GrnReceiptAdditions)->save($this->receiptRequest(), 1);
        $this->assertEquals(2, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(3, DB::table('inventories')->where('id', 7)->value('grn_qty'));
        $this->assertEquals(1, DB::table('inventories')->count());
        $this->assertEquals(1, DB::table('product_warranty_serials')->where('id', 20)->value('status'));
        $this->assertEquals(5, DB::table('product_warranty_serials')->where('id', 20)->value('grn_product_id'));
        $this->assertEquals(3, DB::table('product_warranty_serials')->count());
        $this->assertEquals(3, DB::table('stock_transactions')->where('id', 9)->value('quantity'));
        $this->assertEquals(2, DB::table('account_transactions')->count());
        $this->assertEquals(100, DB::table('account_transactions')->where('debit_credit', 1)->sum('amount'));
        $this->assertEquals(300, DB::table('grns')->where('id', 1)->value('total_amount'));
    }

    public function test_invalid_sold_serial_change_rolls_back_every_stock_write()
    {
        $request = $this->receiptRequest();
        $units = json_decode($request->serial_warranty_data, true);
        $units['product_10'][0]['serial'] = 'CHANGED';
        $request->merge(['serial_warranty_data' => json_encode($units)]);
        try {
            (new GrnReceiptAdditions)->save($request, 1);
            $this->fail('Sold serial modification was allowed.');
        } catch (ValidationException $e) {
            $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('remain_qty'));
            $this->assertEquals(2, DB::table('grn_products')->where('id', 5)->value('received_qty'));
            $this->assertEquals(0, DB::table('account_transactions')->count());
        }
    }

    public function test_admin_can_append_a_different_product_without_replacing_sold_stock()
    {
        $request = $this->receiptRequest();
        $items = $request->items;
        $items[] = ['item' => 11, 'quantity' => 1, 'receive_quantity' => 1, 'unit_cost' => 50, 'grn_cost' => 50, 'sale_price' => 60, 'hire_price' => 83];
        $units = json_decode($request->serial_warranty_data, true);
        $units['product_11'] = [['serial' => 'SECOND', 'warranty' => 'W4']];
        $request->merge(['items' => $items, 'serial_warranty_data' => json_encode($units)]);
        (new GrnReceiptAdditions)->save($request, 1);
        $this->assertEquals(2, DB::table('inventories')->count());
        $this->assertEquals(1, DB::table('inventories')->where('product_id', 11)->value('remain_qty'));
        $this->assertEquals(350, DB::table('grns')->where('id', 1)->value('total_amount'));
    }

    public function test_new_unit_document_upload_is_saved_without_changing_sold_units()
    {
        $request = $this->receiptRequest();
        $request->files->set('warranty_documents', ['product_10' => [2 => UploadedFile::fake()->create('warranty.pdf', 1, 'application/pdf')]]);
        $path = null;
        try {
            (new GrnReceiptAdditions)->save($request, 1);
            $path = DB::table('product_warranty_serials')->where('serial_number', 'NEW')->value('warranty_document');
            $this->assertNotEmpty($path);
            $this->assertFileExists(public_path($path));
            $this->assertEquals(1, DB::table('product_warranty_serials')->where('id', 20)->value('status'));
        } finally {
            if ($path && is_file(public_path($path))) unlink(public_path($path));
        }
    }

    public function test_duplicate_submission_does_not_add_stock_or_accounting_twice()
    {
        $request = $this->receiptRequest();
        $service = new GrnReceiptAdditions;
        $service->save($request, 1);
        $unitId = DB::table('product_warranty_serials')->where('serial_number', 'NEW')->value('id');
        $service->save($request, 1);
        $this->assertEquals(3, DB::table('inventories')->where('id', 7)->value('grn_qty'));
        $this->assertEquals(2, DB::table('account_transactions')->count());
        $this->assertEquals($unitId, DB::table('product_warranty_serials')->where('serial_number', 'NEW')->value('id'));
    }

    public function test_admin_price_correction_updates_receipt_without_replacing_sold_units()
    {
        $request = $this->receiptRequest();
        $items = $request->items;
        $items[0]['grn_cost'] = 200;
        $request->merge(['items' => $items]);
        (new GrnReceiptAdditions)->save($request, 1);
        $this->assertEquals(200, DB::table('inventories')->where('id', 7)->value('purchase_price'));
        $this->assertEquals(600, DB::table('grns')->where('id', 1)->value('total_amount'));
        $this->assertEquals(400, DB::table('account_transactions')->where('debit_credit', 1)->sum('amount'));
        $this->assertEquals(1, DB::table('product_warranty_serials')->where('id', 20)->value('status'));
    }

    public function test_furniture_card_number_is_optional_but_non_furniture_card_is_required()
    {
        $request = $this->receiptRequest();
        $units = json_decode($request->serial_warranty_data, true);
        $units['product_10'][2]['warranty'] = '';
        $lines = \App\GrnProduct::where('grn_id', 1)->get()->keyBy('id');
        DB::table('product_categories')->where('id', 1)->update(['name' => 'Furniture']);
        \App\Services\GrnWarrantyRules::validate($request->items, $units, $lines);
        $this->assertEquals(2, DB::table('product_warranty_serials')->count());
        DB::table('product_categories')->where('id', 1)->update(['name' => 'Electronics']);
        $this->expectException(ValidationException::class);
        \App\Services\GrnWarrantyRules::validate($request->items, $units, $lines);
    }

    public function test_furniture_still_requires_serial_number_or_color()
    {
        $request = $this->receiptRequest();
        $units = json_decode($request->serial_warranty_data, true);
        $units['product_10'][2]['serial'] = '';
        DB::table('product_categories')->where('id', 1)->update(['name' => 'Furniture']);
        $this->expectException(ValidationException::class);
        \App\Services\GrnWarrantyRules::validate($request->items, $units, \App\GrnProduct::where('grn_id', 1)->get()->keyBy('id'));
    }

    public function test_manual_receipt_existing_item_can_reduce_quantity_and_change_prices_and_warranty()
    {
        DB::table('inventories')->where('id', 7)->update(['remain_qty' => 2]);
        DB::table('product_warranty_serials')->where('id', 20)->update(['status' => 0]);
        $request = $this->receiptRequest();
        $items = $request->items;
        $items[0] = array_merge($items[0], ['quantity' => 1, 'receive_quantity' => 1,
            'unit_cost' => 150, 'grn_cost' => 150, 'sale_price' => 200, 'hire_price' => 276]);
        $request->merge(['items' => $items, 'serial_warranty_data' => json_encode(['product_10' => [
            ['id' => 20, 'serial' => 'CORRECTED', 'warranty' => 'WC', 'warranty_period' => 24,
                'additional_warranty_description' => 'Updated terms'],
        ]])]);
        (new GrnReceiptAdditions)->save($request, 1);
        $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('remain_qty'));
        $this->assertEquals(1, DB::table('inventories')->where('id', 7)->value('grn_qty'));
        $this->assertEquals(150, DB::table('inventories')->where('id', 7)->value('purchase_price'));
        $this->assertEquals(200, DB::table('inventories')->where('id', 7)->value('sale_price'));
        $this->assertEquals(150, DB::table('grns')->where('id', 1)->value('total_amount'));
        $this->assertEquals(24, DB::table('product_warranty_serials')->where('id', 20)->value('warranty_period'));
        $this->assertSame('Updated terms', DB::table('product_warranty_serials')->where('id', 20)->value('additional_warranty_description'));
        $this->assertEquals(1, DB::table('product_warranty_serials')->count());
        $this->assertEquals(1, DB::table('stock_transactions')->where('id', 9)->value('quantity'));
        $this->assertEquals(50, DB::table('account_transactions')->where('chart_of_account_id', 5)->where('debit_credit', 0)->sum('amount'));
    }

    public function test_quantity_cannot_drop_below_sold_units_or_discard_a_sold_serial()
    {
        $request = $this->receiptRequest();
        $items = $request->items;
        $items[0]['quantity'] = 1;
        $items[0]['receive_quantity'] = 1;
        $request->merge(['items' => $items, 'serial_warranty_data' => json_encode(['product_10' => [
            ['id' => 21, 'serial' => 'AVAILABLE', 'warranty' => 'W2'],
        ]])]);
        try {
            (new GrnReceiptAdditions)->save($request, 1);
            $this->fail('Sold serial was discarded.');
        } catch (ValidationException $e) {
            $this->assertEquals(2, DB::table('inventories')->where('id', 7)->value('grn_qty'));
            $this->assertNotNull(DB::table('product_warranty_serials')->where('id', 20)->first());
        }
    }
}
