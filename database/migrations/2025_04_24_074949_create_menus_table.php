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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('type', ['link', 'dropdown', 'label'])->default('link');
            $table->enum('position', ['top', 'side'])->default('side');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order')->nullable();
            $table->timestamps();
            // Foreign key constraint for parent_id
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
        // Create pivot table for many-to-many relationship
        Schema::create('menu_roles', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['menu_id', 'role_id']);
        });
        // Create a pivot table for many-to-many relationship between menus and users
        Schema::create('menu_users', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['menu_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
