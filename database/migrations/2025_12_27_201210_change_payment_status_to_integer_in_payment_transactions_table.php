<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_status')->change();
            $table->foreign('payment_status')->references('id')->on('payment_statuses')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_status']);
            $table->string('payment_status')->change();
        });
    }
};
