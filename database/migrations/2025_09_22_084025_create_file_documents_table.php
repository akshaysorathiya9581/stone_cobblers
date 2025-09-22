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
        Schema::create('file_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();    // for customer/user
            $table->unsignedBigInteger('project_id')->nullable()->index(); // for project
            $table->string('name', 191);
            $table->string('path', 1024);
            $table->string('mime', 100)->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('category', 191)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_documents');
    }
};
