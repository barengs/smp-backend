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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_type', 50);
            $table->text('description')->nullable();
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['SUCCESS', 'PENDING', 'FAILED', 'REVERSED']);
            $table->string('reference_number', 50)->unique();
            $table->string('channel', 20);
            $table->string('source_account', 20)->nullable();
            $table->string('destination_account', 20)->nullable();
            $table->timestamps();

            $table->foreign('source_account')->references('account_number')->on('accounts')->onDelete('restrict');
            $table->foreign('destination_account')->references('account_number')->on('accounts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
