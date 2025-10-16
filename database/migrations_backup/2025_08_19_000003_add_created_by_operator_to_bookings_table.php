<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_operator')->nullable()->after('created_by_furriel');
            $table->foreign('created_by_operator')->references('id')->on('users')->onDelete('set null');
            $table->index('created_by_operator');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['created_by_operator']);
            $table->dropIndex(['created_by_operator']);
            $table->dropColumn('created_by_operator');
        });
    }
};
