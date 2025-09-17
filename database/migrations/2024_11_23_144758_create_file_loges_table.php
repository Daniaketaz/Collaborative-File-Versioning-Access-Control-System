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
        Schema::create('file_loges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->nullable()->constrained('files')->references('id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->references('id')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->references('id')->onDelete('cascade');
            $table->string('action')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_loges');
    }
};
