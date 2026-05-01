<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->integer('max_points')->default(100)->after('weight');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->dropColumn('max_points');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
