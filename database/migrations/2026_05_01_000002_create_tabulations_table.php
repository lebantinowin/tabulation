<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contestant_id')->constrained()->onDelete('cascade');
            $table->decimal('total_score', 10, 4)->nullable();
            $table->integer('rank')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabulations');
    }
};
