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
        Schema::create('news_category_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('user_email');
            $table->string('user_name')->nullable();
            $table->string('unsubscription_key')->nullable();
            $table->unique(['news_category_id', 'user_email']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_category_subscriptions');
    }
};
