<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class CsvConvertExport implements FromCollection
{
    /*
    public function __construct(int $file_id)
    {
        $this->file_id = $file_id;
    }*/

    public function collection()
    {
        return new Collection([
            [
                'name' => 'Mourad'
            ],
            [
                'name' => 'Smail'
            ]
        ]);
    }
}
