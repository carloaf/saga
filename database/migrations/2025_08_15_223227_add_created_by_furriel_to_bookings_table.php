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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_furriel')->nullable()->after('status');
            $table->foreign('created_by_furriel')->references('id')->on('users')->onDelete('set null');
            $table->index('created_by_furriel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['created_by_furriel']);
            $table->dropIndex(['created_by_furriel']);
            $table->dropColumn('created_by_furriel');
        });
    }
};
