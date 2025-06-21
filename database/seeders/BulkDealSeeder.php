<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BulkDeal;

class BulkDealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bulkDeals = [
            [
                'name' => 'Luxury Sedan Deal 2024',
                'description' => 'Bulk purchase of luxury sedans from European manufacturers',
                'total_value' => 2500000.00,
                'status' => 'active'
            ],
            [
                'name' => 'SUV Fleet Acquisition',
                'description' => 'Fleet of SUVs for corporate clients',
                'total_value' => 1800000.00,
                'status' => 'active'
            ],
            [
                'name' => 'Electric Vehicle Initiative',
                'description' => 'Green initiative - electric and hybrid vehicles',
                'total_value' => 3200000.00,
                'status' => 'active'
            ],
            [
                'name' => 'Sports Car Collection',
                'description' => 'High-performance sports cars for premium market',
                'total_value' => 4500000.00,
                'status' => 'active'
            ],
            [
                'name' => 'Commercial Vehicle Deal',
                'description' => 'Commercial vehicles for business operations',
                'total_value' => 1200000.00,
                'status' => 'active'
            ]
        ];

        foreach ($bulkDeals as $deal) {
            BulkDeal::create($deal);
        }
    }
}
