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
        Schema::create('policy_economic_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->json('purpose_of_policy_and_structure')->nullable();
            $table->text('additional_details')->nullable();
            $table->double('estimated_networth')->nullable();
            $table->text('source_of_wealth_for_policy')->nullable();
            $table->text('distribution_strategy_during_policy_lifetime')->nullable();
            $table->text('distribution_strategy_post_death_payout')->nullable();
            $table->text('known_triggers_for_policy_exit_or_surrender')->nullable();
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
        Schema::dropIfExists('policy_economic_profiles');
    }
};
