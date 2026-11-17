<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('phone_mobile2')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_duration')->nullable();
            $table->string('business_duration_on_address')->nullable();
            $table->tinyInteger('business_registration')->nullable();
            $table->tinyInteger('business_partners')->nullable();
            $table->tinyInteger('business_employees')->nullable();
            $table->tinyInteger('business_insurance')->nullable();
            $table->tinyInteger('business_bank_accounts')->nullable();
            $table->string('business_bank_name')->nullable();
            $table->string('business_bank_account_number')->nullable();
            $table->string('business_bank_type')->nullable();
            $table->tinyInteger('any_other_income')->nullable();
            $table->string('any_other_income_details')->nullable();
            $table->tinyInteger('expetation_for_starter_up')->nullable();
            $table->string('expetation_for_starter_up_details')->nullable();
            $table->tinyInteger('insurance')->nullable();
            $table->tinyInteger('vahecle_count')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_finance')->nullable();
            $table->string('vehicle_finance_amount')->nullable();
            $table->string('other_loan')->nullable();
            $table->string('other_loan_company')->nullable();
            $table->string('other_loan_amount')->nullable();
            $table->string('other_loan_installment')->nullable();
            $table->string('children_names')->nullable();

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
        Schema::dropIfExists('customer_details');
    }
}
