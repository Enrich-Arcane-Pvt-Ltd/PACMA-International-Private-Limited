<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToStockAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_adjustments', 'status')) {
                $table->tinyInteger('status')->default(1)->after('action');
            }
            if (!Schema::hasColumn('stock_adjustments', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('stock_adjustments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('stock_adjustments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
}
