<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DebugImport extends Command
{
    protected $signature = 'debug:import';
    protected $description = 'Debug import';

    public function handle(): int
    {
        $file = public_path('backend_assets/file/sample_import.xlsx');

        // Test với Maatwebsite
        $headings = (new HeadingRowImport(2))->toArray($file);
        $this->info('Maatwebsite headings:');
        print_r($headings[0][0] ?? []);

        // Test với PhpSpreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheetByName('Import');

        $this->info("\nPhpSpreadsheet Row 2:");
        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[$cell->getColumn()] = $cell->getValue();
            }
            print_r($cells);
        }

        $this->info("\nPhpSpreadsheet Row 3 (data):");
        foreach ($sheet->getRowIterator(3) as $row) {
            $cellIterator = $row->getCellIterator();
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[$cell->getColumn()] = $cell->getValue();
            }
            print_r($cells);
        }

        return 0;
    }
}
