<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierOffersExport implements FromCollection/* , WithHeadings */
{
    protected $data;
    protected $suppliers;

    public function __construct($data, $suppliers)
    {
        $this->data = $data;
        $this->suppliers = $suppliers;
    }

    public function collection()
    {
        return collect($this->data);
    }

    /* public function headings(): array
    {
        $headings = ['NO', 'ITEM / 物品', 'UNIT / 单位'];

        foreach ($this->suppliers as $supplier) {
            $headings[] = $supplier->name . ' / ' . $supplier->name_cn;
        }

        return $headings;
    } */
}
