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
                'level' => 'header',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '200000',
                'account_name' => 'KEWAJIBAN',
                'account_type' => 'Liability',
                'level' => 'header',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '300000',
                'account_name' => 'EKUITAS',
                'account_type' => 'Equity',
                'level' => 'header',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '400000',
                'account_name' => 'PENDAPATAN',
                'account_type' => 'Revenue',
                'level' => 'header',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '420000',
                'account_name' => 'Pendapatan Operasional Selain Bunga',
                'account_type' => 'Revenue',
                'level' => 'subheader',
                'parent_coa_code' => '400000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '421000',
                'account_name' => 'Pendapatan Jasa dan Administrasi',
                'account_type' => 'Revenue',
                'level' => 'subheader',
                'parent_coa_code' => '420000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '421100',
                'account_name' => 'Pendapatan Terkait Rekening',
                'account_type' => 'Revenue',
                'level' => 'subheader',
                'parent_coa_code' => '421000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '421101',
                'account_name' => 'Pendapatan Biaya Pendaftaran',
                'account_type' => 'Revenue',
                'level' => 'detail',
                'parent_coa_code' => '421100',
                'is_postable' => 'True',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '500000',
                'account_name' => 'BEBAN',
                'account_type' => 'Expense',
                'level' => 'header',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '110000',
                'account_name' => 'Kas dan Setara Kas',
                'account_type' => 'Assets',
                'level' => 'subheader',
                'parent_coa_code' => '100000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '111000',
                'account_name' => 'Kas',
                'account_type' => 'Assets',
                'level' => 'subheader',
                'parent_coa_code' => '110000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '111100',
                'account_name' => 'Kas di Khazanah',
                'account_type' => 'Assets',
                'level' => 'subheader',
                'parent_coa_code' => '111000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '111101',
                'account_name' => 'Kas di Teller',
                'account_type' => 'Assets',
                'level' => 'detail',
                'parent_coa_code' => '111100',
                'is_postable' => 'True',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '111102',
                'account_name' => 'Kas di ATM',
                'account_type' => 'Assets',
                'level' => 'detail',
                'parent_coa_code' => '111100',
                'is_postable' => 'True',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '210000',
                'account_name' => 'Simpanan Nasabah',
                'account_type' => 'Liability',
                'level' => 'subheader',
                'parent_coa_code' => '200000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '211000',
                'account_name' => 'Simpanan Bentuk Tabungan',
                'account_type' => 'Liability',
                'level' => 'subheader',
                'parent_coa_code' => '210000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '211100',
                'account_name' => 'Tabungan Rupiah',
                'account_type' => 'Liability',
                'level' => 'subheader',
                'parent_coa_code' => '211000',
                'is_postable' => 'False',
                'is_active' => 'True'
            ],
            [
                'coa_code' => '211101',
                'account_name' => 'Simpanan Tabungan',
                'account_type' => 'Liability',
                'level' => 'detail',
                'parent_coa_code' => '211100',
                'is_postable' => 'True',
                'is_active' => 'True'
            ],
        ];

        foreach ($data_coa as $coa) {
            \App\Models\ChartOfAccount::create($coa);
        }
    }
}
