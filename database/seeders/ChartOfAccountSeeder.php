<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data_coa = [
            [
                'coa_code' => '100000',
                'account_name' => 'ASET',
                'account_type' => 'Asset',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '200000',
                'account_name' => 'KEWAJIBAN',
                'account_type' => 'Liability',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '300000',
                'account_name' => 'EKUITAS',
                'account_type' => 'Equity',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '400000',
                'account_name' => 'PENDAPATAN',
                'account_type' => 'Revenue',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '500000',
                'account_name' => 'BEBAN',
                'account_type' => 'Expense',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '110000',
                'account_name' => 'Kas dan Setara Kas',
                'account_type' => 'Assets',
                'parent_coa_code' => '100000',
                'is_postable' => false,
                'is_active' => true
            ],
            [
                'coa_code' => '210000',
                'account_name' => 'Simpanan Nasabah',
                'account_type' => 'Liability',
                'parent_coa_code' => '200000',
                'is_postable' => false,
                'is_active' => true
            ]
        ];

        foreach ($data_coa as $coa) {
            \App\Models\ChartOfAccount::create($coa);
        }
    }
}
