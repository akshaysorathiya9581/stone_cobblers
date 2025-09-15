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
        Schema::create('kitchen_quotes', function (Blueprint $table) {
            $table->id();

            $table->string('project')->nullable();
            // use string/text instead of enum
            $table->string('type', 50)->default('kitchen_quote');

            $table->decimal('cost', 12, 4)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_quotes');
    }
};
