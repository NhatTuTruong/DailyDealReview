<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// <--- 1. Import cái này
use Maatwebsite\Excel\Concerns\WithStyles;

// <--- 2. Import cái này để style (Optional)
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Lấy dữ liệu
     */
    public function collection()
    {
        return Category::where('type', Category::CATEGORY_TYPE_STORE)
            ->active()
            ->orderBy('name')
            ->select('id', 'name')
            ->get();
    }

    /**
     * Header columns
     */
    public function headings(): array
    {
        return [
            'ID',
            'Category Name',
        ];
    }

    /**
     * Map dữ liệu
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
        ];
    }

    /**
     * 3. Style cho dòng tiêu đề in đậm (Optional)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Dòng 1 (Tiêu đề) sẽ in đậm
            1 => ['font' => ['bold' => true]],
        ];
    }
}
