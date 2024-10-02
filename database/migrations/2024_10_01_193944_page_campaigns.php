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
        Schema::create('page_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->unique(); // Armazena o ID da página
            $table->string('last_activity_campaign')->nullable(); // Data e hora da última campanha
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
