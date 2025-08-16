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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 20)->unique();
            $table->string('product_name', 100);
            $table->enum('product_type', ['Tabungan', 'Deposito', 'Pinjaman'])->default('Tabungan');
            $table->decimal('interest_rate', 8, 4)->nullable(); // Suku bunga dalam persen
            $table->decimal('admin_fee', 18, 2)->default(0); // Biaya administrasi bulanan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
