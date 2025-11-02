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
        Schema::table('quote_items', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('name');
            $table->decimal('tax_cost', 15, 4)->default(0)->after('line_total');
            $table->boolean('is_taxable')->default(false)->after('tax_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn(['type', 'tax_cost', 'is_taxable']);
        });
    }
};

