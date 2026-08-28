<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateSampleImport extends Command
{
    protected $signature = 'import:sample-store';
    protected $description = 'Generate sample Excel file for store import';

    public function handle(): int
    {
        $categories = Category::where('language', app()->getLocale())
            ->where('type', Category::CATEGORY_TYPE_STORE)
            ->orderBy('priority')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($categories->isEmpty()) {
            $this->error('Không tìm thấy danh mục nào!');
            return 1;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import');

        // Dòng 1: Tiêu đề nhóm
        $sheet->setCellValue('A1', 'THÔNG TIN CỬA HÀNG');
        $sheet->setCellValue('E1', 'THÔNG TIN OFFER');

        // Dòng 2: Header tiếng Việt
        $headers = ['Tên cửa hàng', 'Danh mục', 'Domain', 'Giới thiệu', 'Max Offer', 'Tên Offer', 'Mã coupon', 'Nội dung khuyến mãi', 'URL'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue($columns[$i] . '2', $header);
        }

        $categoryNames = $categories->pluck('name')->toArray();
        $storeName = 'Shopee';

        // Dòng 3: Ví dụ Store mới (dữ liệu mẫu tiếng Anh)
        $examples = [
            $storeName,
            $categories->first()->name,
            'shopee.vn',
            'Leading e-commerce platform in Southeast Asia',
            '50% Off',
            '',
            'SALE20',
            '20% Off',
            'https://shopee.vn'
        ];

        foreach ($examples as $i => $value) {
            $sheet->setCellValue($columns[$i] . '3', $value);
        }

        // Dòng 4-6: Chỉ có Offer (cửa hàng trống → kế thừa store phía trên)
        $offerExamples = [
            ['', 'NEWUSER15', '15% Off'],
            ['', 'FREESHIP', 'Free Shipping'],
            ['', 'SHOP10', '$10 Off'],
        ];

        foreach ($offerExamples as $idx => $offer) {
            $rowNum = 4 + $idx;
            $sheet->setCellValue('F' . $rowNum, $offer[0]); // Tên Offer (trống = auto)
            $sheet->setCellValue('G' . $rowNum, $offer[1]); // Mã coupon
            $sheet->setCellValue('H' . $rowNum, $offer[2]); // Nội dung khuyến mãi
        }

        // Dòng 7: Store mới (Lazada)
        $storeName2 = 'Lazada';
        $sheet->setCellValue('A7', $storeName2);
        $sheet->setCellValue('B7', $categories->skip(1)->first()->name ?? $categories->first()->name);
        $sheet->setCellValue('C7', 'lazada.vn');
        $sheet->setCellValue('D7', 'Leading online shopping platform');
        $sheet->setCellValue('E7', '40% Off');
        $sheet->setCellValue('F7', '');
        $sheet->setCellValue('G7', 'LAZ40');
        $sheet->setCellValue('H7', '40% Off');
        $sheet->setCellValue('I7', 'https://lazada.vn');

        // Dòng 8: Offer thuộc Lazada
        $sheet->setCellValue('F8', '');
        $sheet->setCellValue('G8', 'FLASH50');
        $sheet->setCellValue('H8', '50% Off');

        // Style cho dòng tiêu đề
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E2E2']]
        ]);
        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']]
        ]);

        // Auto width
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Danh sách danh mục (để copy)
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Danh mục');
        $sheet2->setCellValue('A1', 'Danh sách danh mục (Copy tên danh mục vào cột "Danh mục" bên sheet chính)');
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']]
        ]);

        $row = 2;
        foreach ($categories as $cat) {
            $sheet2->setCellValue('A' . $row, $cat->name);
            $row++;
        }
        $sheet2->getColumnDimension('A')->setAutoSize(true);

        $outputPath = public_path('backend_assets/file/sample_import.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        $this->info("Đã tạo file mẫu: {$outputPath}");
        $this->info("Danh mục có sẵn: " . implode(', ', $categoryNames));

        return 0;
    }
}
