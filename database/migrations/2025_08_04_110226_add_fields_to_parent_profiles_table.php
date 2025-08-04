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
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->dropColumn('occupation'); // Remove the old occupation column
            // Add the new occupation_id column
            $table->string('occupation_id')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            //
        });
    }
};
