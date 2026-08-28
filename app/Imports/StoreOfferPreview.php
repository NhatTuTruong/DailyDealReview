<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Row;

class StoreOfferPreview implements OnEachRow, WithHeadingRow, WithMultipleSheets
{
    public int $storeCount = 0;
    public int $offerCount = 0;
    public array $stores = [];

    public function headingRow(): int
    {
        return 2;
    }

    public function sheets(): array
    {
        return [
            'Import' => $this,
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();

        // Skip header rows (row 1 and 2)
        if ($rowIndex <= 2) {
            return;
        }

        $rowData = $row->toArray();

        // Skip empty rows
        if (empty(array_filter($rowData))) {
            return;
        }

        $storeName = trim($rowData['store_name'] ?? $rowData['ten_cua_hang'] ?? '');

        if (!empty($storeName)) {
            $this->storeCount++;
            $this->stores[] = $storeName;
        }

        // Mỗi dòng data = 1 offer (tính cả dòng có và không có store name)
        $this->offerCount++;
    }
}
