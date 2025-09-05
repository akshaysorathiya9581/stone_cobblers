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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zipCode', 20)->nullable()->after('state');
            $table->text('additionalNotes')->nullable()->after('zipCode');
            $table->string('referralSource')->nullable()->after('additionalNotes');
            $table->string('status')->default('active')->after('referralSource');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'address',
                'city',
                'state',
                'zipCode',
                'additionalNotes',
                'referralSource',
                'status',
            ]);
        });
    }
};
