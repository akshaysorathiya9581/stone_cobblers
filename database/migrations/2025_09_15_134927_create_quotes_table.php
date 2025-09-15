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
            $table->unsignedBigInteger('user_id');           // linked to auth user (customer or staff)
            $table->string('quote_number');        // e.g. QT-2024-001
            $table->string('customer_name')->nullable();     // optional free text
            $table->string('project_name')->nullable();      // optional project label
            $table->decimal('final_total', 15, 2)->default(0);
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
