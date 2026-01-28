<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('machine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['machine_id', 'is_featured']);
            $table->index(['machine_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_images');
    }
};
