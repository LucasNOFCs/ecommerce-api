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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('label');

            $table->string('recipient_name');

            $table->string('street');

            $table->string('number');

            $table->string('complement')
                ->nullable();

            $table->string('neighborhood');

            $table->string('city');

            $table->string('state');

            $table->string('postal_code');

            $table->string('country')
                ->defualt('Brazil');

            $table->string('is_default')
                ->default(false);

            $table->timestamps();

            $table->index([
                'user_id',
                'is_default',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
