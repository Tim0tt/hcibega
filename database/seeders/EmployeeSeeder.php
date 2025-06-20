<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('employees')->truncate();
        
        // Create test employees for concurrent testing
        $employees = [
            'John Doe',
            'Jane Smith',
            'Bob Johnson',
            'Alice Brown',
            'Charlie Wilson',
            'Diana Davis',
            'Edward Miller',
            'Fiona Garcia',
            'George Martinez',
            'Helen Rodriguez',
            'Ivan Lopez',
            'Julia Gonzalez',
            'Kevin Anderson',
            'Linda Thomas',
            'Michael Taylor',
            'Nancy Moore',
            'Oscar Jackson',
            'Patricia Martin',
            'Quincy Lee',
            'Rachel Perez',
        ];
        
        foreach ($employees as $name) {
            Employee::create([
                'full_name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Created ' . count($employees) . ' test employees for concurrent testing.');
    }
}