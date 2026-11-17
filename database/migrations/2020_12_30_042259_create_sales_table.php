<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_of_sale');
            $table->integer('customer_id');
            $table->integer('guarantor_id');
            $table->string('sale_type',25);
            $table->integer('product_id');
            $table->float('unit_price',10,2);
            $table->integer('quantity');
            $table->float('gross_amount',10,2);
            $table->float('discount',10,2);
            $table->float('net_amount',10,2);
            $table->float('down_payment',10,2);
            $table->float('outstanding_amount',10,2);
            $table->integer('installment');
            $table->float('interest_rate',10,2);
            $table->float('interest_amount',10,2);
            $table->float('total_due_amount',10,2);
            $table->float('monthly_installment',10,2);
            $table->date('due_date');
            $table->integer('status');
            $table->integer('created_by')->default(2);
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
        Schema::dropIfExists('sales');
    }
}
