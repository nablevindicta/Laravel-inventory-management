<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            $table->integer('opname_month')->nullable()->after('keterangan');
            $table->integer('opname_year')->nullable()->after('opname_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            $table->dropColumn(['opname_month', 'opname_year']);
        });
    }
};