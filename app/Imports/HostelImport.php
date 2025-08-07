<?php

namespace App\Imports;

use App\Models\Hostel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HostelImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Hostel([
            'name' => $row['name'],
            'parent_id' => $row['parent_id'],
            'description' => $row['description'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:hostels,id',
            'description' => 'nullable|string',
        ];
    }
}
