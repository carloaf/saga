<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->unique();
            $table->string('full_name');
            $table->string('war_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar_url')->nullable();
            $table->foreignId('rank_id')->constrained('ranks');
            $table->foreignId('organization_id')->constrained('organizations');
            $table->enum('gender', ['male', 'female']);
            $table->date('ready_at_om_date');
            $table->enum('role', ['user', 'superuser'])->default('user');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
