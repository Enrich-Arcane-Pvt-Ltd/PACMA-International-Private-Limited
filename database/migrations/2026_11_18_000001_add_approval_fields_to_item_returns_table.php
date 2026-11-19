<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToItemReturnsTable extends Migration
{
    public function up()
    {
        Schema::table('item_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('item_returns', 'meta')) {
                $table->text('meta')->nullable()->after('narration');
            }
            if (!Schema::hasColumn('item_returns', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('item_returns', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('item_returns', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('item_returns', function (Blueprint $table) {
            $table->dropColumn(['meta', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
}
