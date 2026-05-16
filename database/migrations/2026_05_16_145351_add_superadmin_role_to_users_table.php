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
        // Alter ENUM to include 'superadmin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'judge') NOT NULL DEFAULT 'judge'");
        
        // Update existing admin to superadmin
        DB::table('users')->where('role', 'admin')->update(['role' => 'superadmin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'judge') NOT NULL DEFAULT 'judge'");
    }
};
