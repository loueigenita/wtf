<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// database/seeders/CustomerSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'name' => 'Louei',
            'address' => '123 Main St',
            'email' => 'loueigenita@gmail.com',
            'contact' => '1234567890',
        ]);
    }
}

