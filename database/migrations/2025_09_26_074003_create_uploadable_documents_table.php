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
        Schema::create('uploadable_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('downloadable_document_id')->nullable();
            $table->string('file')->nullable();
            $table->boolean('has_expiry_date')->default(false);
            $table->dateTime('expiry_date')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploadable_documents');
    }
};
