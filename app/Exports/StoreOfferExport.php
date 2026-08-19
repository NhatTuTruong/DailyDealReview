<?php

namespace App\Exports;

use App\Models\Offer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StoreOfferExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function query()
    {
        // Truy vấn Offer kèm theo Store để lấy dữ liệu phẳng theo đúng format CSV
        return Offer::query()
            ->with('store')
            ->whereIn('store_id', $this->ids);
    }

    public function headings(): array
    {
        return [
            // Hàng tiêu đề 1: Phân nhóm dữ liệu
            ['STORE DATA', '', '', '', '', '', '', 'OFFER DATA'],
            // Hàng tiêu đề 2: Tên cột chi tiết (khớp chính xác với file sample)
            [
                'store_name', 'store_cat_id', 'store_image', 'store_priority',
                'store_status', 'store_about_store', 'store_max_offer',
                'name', 'code', 'offer', 'url', 'status', 'verified', 'priority'
            ]
        ];
    }

    public function map($offer): array
    {
        $store = $offer->store;
        return [
            // Thông tin Store (Lặp lại cho mỗi offer của store đó)
            $store->name ?? '',
            $store->cat_id ?? '',
            $store->image ?? '',
            $store->priority ?? 0,
            $store->status ?? 1,
            $store->about_store ?? '',
            $store->max_offer ?? '',

            // Thông tin Offer
            $offer->name,
            $offer->code,
            $offer->offer,
            $offer->url,
            $offer->status,
            $offer->verified,
            $offer->priority
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Làm đậm hàng tiêu đề
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}