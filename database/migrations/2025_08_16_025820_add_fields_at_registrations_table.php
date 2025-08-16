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
            $table->string('previous_madrasah')->nullable()->after('education_level_id');
            $table->string('previous_madrasah_address')->nullable()->after('previous_madrasah');
            $table->string('certificate_madrasah')->nullable()->after('previous_madrasah_address');
            $table->unsignedBigInteger('madrasah_level_id')->nullable()->after('certificate_madrasah');
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
