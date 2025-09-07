<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Nama class akan sesuai dengan nama file yang Anda buat
class AddDescriptionToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Perintah untuk MENGUBAH tabel 'transactions'
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom 'description' yang bisa diisi teks dan boleh kosong (nullable)
            // diletakkan setelah kolom 'type' agar rapi
            $table->text('description')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Perintah untuk menghapus kolom 'description' jika migrasi di-rollback
            $table->dropColumn('description');
        });
    }
}