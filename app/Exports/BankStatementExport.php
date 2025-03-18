<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankStatementExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            '№ п/п',
            'Дата операції',
            '% ставка',
            'Операція',
            'Сума',
            'Сума в грн.',
            'Призначення',
            
        ];
    }
}