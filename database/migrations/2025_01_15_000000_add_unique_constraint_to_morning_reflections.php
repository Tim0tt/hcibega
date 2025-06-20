<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('morning_reflections', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate attendance for same employee on same date
            $table->unique(['employee_id', 'date'], 'unique_employee_date_attendance');
        });
    }

    public function down(): void
    {
        Schema::table('morning_reflections', function (Blueprint $table) {
            $table->dropUnique('unique_employee_date_attendance');
        });
    }
};