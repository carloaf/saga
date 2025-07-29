<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('booking_date');
            $table->enum('meal_type', ['breakfast', 'lunch']);
            $table->timestamps();

            // Ensure a user can only book one of each meal type per day
            $table->unique(['user_id', 'booking_date', 'meal_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
