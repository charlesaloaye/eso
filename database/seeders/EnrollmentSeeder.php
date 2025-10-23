<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enrollment::create([
            'guest_email' => 'john.doe@example.com',
            'guest_name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '+1234567890',
            'company_name' => 'Acme Corp',
            'country' => 'United States',
            'street_address' => '123 Main St',
            'town_city' => 'New York',
            'state' => 'NY',
            'total_amount' => 299.99,
            'discounted_amount' => 249.99,
            'status' => 'completed'
        ]);

        Enrollment::create([
            'guest_email' => 'jane.smith@example.com',
            'guest_name' => 'Jane Smith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone_number' => '+1987654321',
            'company_name' => 'Tech Solutions',
            'country' => 'Canada',
            'street_address' => '456 Oak Ave',
            'town_city' => 'Toronto',
            'state' => 'ON',
            'total_amount' => 199.99,
            'discounted_amount' => 199.99,
            'status' => 'pending'
        ]);

        Enrollment::create([
            'guest_email' => 'bob.wilson@example.com',
            'guest_name' => 'Bob Wilson',
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'phone_number' => '+1122334455',
            'company_name' => 'Wilson Enterprises',
            'country' => 'United Kingdom',
            'street_address' => '789 High Street',
            'town_city' => 'London',
            'state' => 'England',
            'total_amount' => 399.99,
            'discounted_amount' => 299.99,
            'status' => 'completed'
        ]);
    }
}
