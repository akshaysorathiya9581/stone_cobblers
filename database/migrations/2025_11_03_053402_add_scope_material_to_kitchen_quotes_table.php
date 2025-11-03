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
        Schema::table('kitchen_quotes', function (Blueprint $table) {
            $table->string('scope_material')->nullable()->after('project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_quotes', function (Blueprint $table) {
            $table->dropColumn('scope_material');
        });
    }
};
