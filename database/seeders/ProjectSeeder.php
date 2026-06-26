<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Project::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $ahmed  = Customer::where('email', 'ahmed@example.com')->first();
        $fatema = Customer::where('email', 'fatema@example.com')->first();
        $karim  = Customer::where('email', 'karim@example.com')->first();
        $rafiq  = Customer::where('email', 'rafiqul@example.com')->first();

        $projects = [
            [
                'customer_id'   => $ahmed->id,
                'project_name'  => 'E-commerce Website',
                'project_code'  => 'PRJ-2026-0001',
                'start_date'    => '2026-01-10',
                'deadline'      => '2026-04-10',
                'budget_amount' => 150000.00,
                'status'        => 'running',
            ],
            [
                'customer_id'   => $ahmed->id,
                'project_name'  => 'Mobile App Development',
                'project_code'  => 'PRJ-2026-0002',
                'start_date'    => '2026-03-01',
                'deadline'      => '2026-07-01',
                'budget_amount' => 250000.00,
                'status'        => 'pending',
            ],
            [
                'customer_id'   => $fatema->id,
                'project_name'  => 'Inventory System',
                'project_code'  => 'PRJ-2026-0003',
                'start_date'    => '2026-01-15',
                'deadline'      => '2026-03-15',
                'budget_amount' => 80000.00,
                'status'        => 'completed',
            ],
            [
                'customer_id'   => $karim->id,
                'project_name'  => 'HR Management System',
                'project_code'  => 'PRJ-2026-0004',
                'start_date'    => '2026-02-01',
                'deadline'      => '2026-06-01',
                'budget_amount' => 200000.00,
                'status'        => 'running',
            ],
            [
                'customer_id'   => $rafiq->id,
                'project_name'  => 'Company Portfolio Website',
                'project_code'  => 'PRJ-2026-0005',
                'start_date'    => '2026-04-01',
                'deadline'      => null,
                'budget_amount' => 30000.00,
                'status'        => 'pending',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}