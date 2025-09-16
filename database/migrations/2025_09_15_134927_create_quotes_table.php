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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');        // linked to auth user (customer or staff)
            $table->string('quote_number');        // e.g. QT-2024-001
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->boolean('is_kitchen')->default(false);
            $table->boolean('is_vanity')->default(false);
            $table->enum('status', ['Draft', 'Sent', 'Approved', 'Rejected', 'Expired'])->default('Draft');
            $table->date('expires_at')->nullable(); // Expiry date of the quote
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
