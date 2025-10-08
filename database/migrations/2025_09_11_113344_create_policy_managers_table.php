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
        Schema::create('policy_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('policy_id')->nullable();
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
        Schema::dropIfExists('policy_managers');
    }
};
