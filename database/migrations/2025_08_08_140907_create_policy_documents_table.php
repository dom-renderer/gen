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
        Schema::create('policy_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('record_id')->nullable()->comment('related to table by document_type');
            $table->enum('document_type', ['policy-holder', 'controlling-person', 'insured-life', 'beneficiary'])->nullable();
            $table->string('document')->nullable();
            $table->boolean('has_expiry_date')->default(1);
            $table->dateTime('expiry_date')->nullable();
            $table->boolean('uploaded')->default(0)->nullable();
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
        Schema::dropIfExists('policy_documents');
    }
};
