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
        Schema::create('stock_opname_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('opname_month');
            $table->integer('opname_year');
            $table->string('title')->nullable(); // Untuk menyimpan judul seperti "Stok Opname September 2023"
            $table->timestamps();
        });

        // Tambahkan foreign key ke tabel stock_opname_logs
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            $table->foreignId('stock_opname_session_id')->nullable()->constrained('stock_opname_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_logs', function (Blueprint $table) {
            $table->dropForeign(['stock_opname_session_id']);
            $table->dropColumn('stock_opname_session_id');
        });
        Schema::dropIfExists('stock_opname_sessions');
    }
};