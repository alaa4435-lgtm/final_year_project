<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')
                ->nullable()
                ->constrained('templates')
                ->cascadeOnDelete(); // NULL = Stage 0 العامة لكل القوالب
            $table->string('title');
            $table->string('slug');
            $table->unsignedInteger('order');
            $table->text('description')->nullable();
            $table->boolean('is_global')->default(false);
            $table->timestamps();

            $table->unique(['template_id', 'slug']);
            $table->index(['template_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
