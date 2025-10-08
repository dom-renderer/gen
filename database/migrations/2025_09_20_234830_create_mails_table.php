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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->string('from_email');
            $table->string('from_name');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->enum('type', ['policy_created', 'policy_updated', 'general'])->default('general');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
