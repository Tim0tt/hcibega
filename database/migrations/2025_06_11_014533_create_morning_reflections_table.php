<?php
     use Illuminate\Database\Migrations\Migration;
     use Illuminate\Database\Schema\Blueprint;
     use Illuminate\Support\Facades\Schema;

     return new class extends Migration
     {
         public function up(): void
         {
             Schema::create('morning_reflections', function (Blueprint $table) {
                 $table->id();
                 $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                 $table->date('date');
                 $table->enum('status', ['Hadir', 'Absen', 'Terlambat']);
                 $table->timestamps();
             });
         }

         public function down(): void
         {
             Schema::dropIfExists('morning_reflections');
         }
     };