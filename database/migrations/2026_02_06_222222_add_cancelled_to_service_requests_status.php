<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status column to include 'Cancelled'
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('Pending', 'Accepted', 'Declined', 'Completed', 'Cancelled') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum options (Warning: this might fail if 'Cancelled' data exists)
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('Pending', 'Accepted', 'Declined', 'Completed') DEFAULT 'Pending'");
    }
};
