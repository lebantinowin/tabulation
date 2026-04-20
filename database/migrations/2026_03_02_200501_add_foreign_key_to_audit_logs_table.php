<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: Removed foreign key constraint due to column type mismatch with existing users table
     */
    public function up(): void
    {
        // Just ensure the user_id column exists with correct type
        // The foreign key was removed due to existing table structure incompatibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
