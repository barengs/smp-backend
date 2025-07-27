<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('parent_id');
            $table->string('nis')->unique();
            $table->string('period')->nullable();
            $table->string('nik')->unique()->nullable();
            $table->string('kk')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('address')->nullable();
            $table->string('born_in')->nullable();
            $table->date('born_at')->nullable();
            $table->unsignedBigInteger('village_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            //
        });
    }
};
