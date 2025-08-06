<?php

namespace App\Imports;

use App\Models\Education;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EducationImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Education([
            'name' => $row['name'],
            'description' => $row['description'],
        ]);
    }
}
