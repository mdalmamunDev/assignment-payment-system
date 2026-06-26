<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $customers = [
            [
                'name'         => 'Ahmed Rahman',
                'email'        => 'ahmed@example.com',
                'phone'        => '01711111111',
                'company_name' => 'Rahman & Sons Ltd',
                'address'      => 'House 12, Road 4, Dhanmondi, Dhaka',
                'status'       => 'active',
            ],
            [
                'name'         => 'Fatema Khanam',
                'email'        => 'fatema@example.com',
                'phone'        => '01722222222',
                'company_name' => 'Khanam Traders',
                'address'      => 'Plot 7, Sector 3, Uttara, Dhaka',
                'status'       => 'active',
            ],
            [
                'name'         => 'Karim Hossain',
                'email'        => 'karim@example.com',
                'phone'        => '01733333333',
                'company_name' => 'Hossain Tech',
                'address'      => 'Agrabad, Chittagong',
                'status'       => 'active',
            ],
            [
                'name'         => 'Nusrat Jahan',
                'email'        => 'nusrat@example.com',
                'phone'        => '01744444444',
                'company_name' => null,
                'address'      => 'GEC Circle, Chittagong',
                'status'       => 'inactive',
            ],
            [
                'name'         => 'Rafiqul Islam',
                'email'        => 'rafiqul@example.com',
                'phone'        => '01755555555',
                'company_name' => 'Islam Enterprises',
                'address'      => 'Shaheb Bazar, Rajshahi',
                'status'       => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}