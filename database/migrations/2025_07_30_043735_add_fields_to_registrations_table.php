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
            $table->string('previous_school')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->string('certificate_number')->nullable();
            $table->unsignedBigInteger('education_level_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('previous_school');
            $table->dropColumn('previous_school_address');
            $table->dropColumn('certificate_number');
            $table->dropColumn('education_level_id');
        });
    }
};
