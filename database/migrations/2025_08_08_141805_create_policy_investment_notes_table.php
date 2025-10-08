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
        Schema::create('policy_investment_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');

            $table->dateTime('date_of_change_portfolio')->nullable();
            $table->text('portfolio_change')->nullable();

            $table->dateTime('date_of_change_idf')->nullable();
            $table->text('idf_change')->nullable();

            $table->dateTime('date_of_change_transfer')->nullable();
            $table->text('transfer_change')->nullable();

            $table->text('decision')->nullable();

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
        Schema::dropIfExists('policy_investment_notes');
    }
};
