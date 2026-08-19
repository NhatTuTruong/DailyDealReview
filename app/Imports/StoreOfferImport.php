<?php

namespace app\Imports;

use App\Models\Offer;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class StoreOfferImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $stores = [];

    /**
     * QUAN TRỌNG: Khai báo dòng tiêu đề là dòng số 2
     * Để bỏ qua dòng 1 (STORE DATA, OFFER DATA)
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            // --- VALIDATE STORE DATA ---
            'store_name' => 'required|string|max:500',
            'store_cat_id' => 'required|integer',
            'store_image' => 'nullable|string',
            'store_priority' => 'nullable|integer|min:0',
            'store_status' => 'nullable|integer|in:0,1',
            'store_max_offer' => 'nullable|string',

            // --- VALIDATE OFFER DATA ---
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'offer' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'status' => 'nullable|integer|in:0,1',
            'verified' => 'nullable|integer|in:0,1',
            'priority' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'store_name.required' => 'Tên Store không được để trống.',
            'store_cat_id.required' => 'Danh mục Store (Cat ID) là bắt buộc.',
            'store_cat_id.integer' => 'Cat ID phải là số nguyên.',
            'store_priority.integer' => 'Thứ tự ưu tiên Store phải là số.',
            'store_status.in' => 'Trạng thái Store chỉ được nhập 0 hoặc 1.',

            'name.required' => 'Tên Offer (cột name) không được để trống.',
            'url.url' => 'Đường dẫn Offer (URL) không đúng định dạng.',
            'status.in' => 'Trạng thái Offer chỉ được nhập 0 hoặc 1.',
            'verified.in' => 'Verified chỉ được nhập 0 hoặc 1.',
            'priority.integer' => 'Thứ tự ưu tiên Offer phải là số.',
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();
        // 2. XỬ LÝ STORE
        // Logic: Kiểm tra xem Store này đã được xử lý trong file này chưa, hoặc đã có trong DB chưa
        $storeName = trim($row['store_name']);
        $max_offer = trim(str_replace('OFF', '', $row['store_max_offer'] ?? '50%'));
        if (isset($this->stores[$storeName])) {
            $store = $this->stores[$storeName];
        } else {
            // Nếu chưa có, tìm trong DB hoặc tạo mới
            // Sử dụng firstOrCreate để tránh duplicate
            $store = Store::firstOrCreate(
                ['name' => $storeName], // Điều kiện tìm kiếm
                [
                    'slug' => Str::slug($storeName),
                    'cat_id' => $row['store_cat_id'] ?? 0,
                    'image' => $row['store_image'] ?? null,
                    'description' => Setting::getRandomShortDescription($storeName),
                    'priority' => $row['store_priority'] ?? 0,
                    'status' => $row['store_status'] ?? 1,
                    'about_store' => $row['store_about_store'] ?? null,
                    'max_offer' => $max_offer,
                    'user_id' => Auth::id() ?? 1, // Lấy ID người đang login
                    'language' => \App::getLocale(),
                    'meta_title' => Setting::generateMetaSEO($storeName, $max_offer),
                    'meta_keywords' => Setting::generateMetaSEO($storeName, $max_offer, 'meta_keywords'),
                    'meta_description' => Setting::generateMetaSEO($storeName, $max_offer, 'meta_description'),
                ]
            );

            // Lưu vào cache để dòng tiếp theo dùng lại luôn, ko cần query DB nữa
            $this->stores[$storeName] = $store;
        }

        // 3. XỬ LÝ OFFER
        // Sau khi đã có $store object (vừa tạo hoặc vừa tìm thấy), ta tạo Offer

        Offer::create([
            'store_id' => $store->id, // LẤY ID TỪ STORE VỪA XỬ LÝ
            'name' => $row['name'],
            'code' => $row['code'] ?? null,
            'offer' => $row['offer'] ?? null,
            'url' => $row['url'] ?? null,
            'description' => Setting::getRandomShortDescription($store->name),
            'status' => $row['status'] ?? 1,
            'user_id' => Auth::id() ?? 1,
            'verified' => $row['verified'] ?? 0,
            'priority' => $row['priority'] ?? 0,
            'language' => \App::getLocale(),
        ]);
    }
}