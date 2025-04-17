<?php

namespace App\Imports;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PermissionImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // If a row is empty, return null to skip it
        if (empty($row['name']) || empty($row['group_name'])) {
            return null; // Skip empty rows
        }

        // Define the attributes to check for duplicates
        $attributes = [
            'name' => $row['name'],
            'guard_name' => 'web', // Adjust this if you have a different guard name
        ];

        // Update or create the Permission record
        return Permission::updateOrCreate($attributes, [
            'group_name' => $row['group_name'], // Update the group_name or set it if creating
            'updated_at' => now(), // Set the updated_at timestamp
            // You can add any other fields you want to update or set here
        ]);
    }

    // Define the chunk size to process large files
    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
