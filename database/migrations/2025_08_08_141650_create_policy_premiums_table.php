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
        Schema::create('policy_premiums', function (Blueprint $table) {
            $table->id();
            $table->string('policy_type');
            $table->unsignedBigInteger('policy_id');
            $table->double('proposed_premium_amount')->default(0);
            $table->text('proposed_premium_note')->nullable();
            $table->double('final_premium_amount')->default(0);
            $table->text('final_premium_note')->nullable();
            $table->enum('premium_frequency', [
                'monthly', 
                'quarterly', 
                'semi-annual', 
                'annual',
                '2-pay',
                '3-pay',
                '4-pay',
                '5-pay',
                '6-pay',
                '7-pay'
            ])->default('monthly')->change();
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
        Schema::dropIfExists('policy_premiums');
    }
};
