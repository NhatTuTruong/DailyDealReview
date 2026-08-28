<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckStoreFields extends Command
{
    protected $signature = 'check:store-fields';
    protected $description = 'Check store fields';

    public function handle(): int
    {
        // Lấy tất cả columns của bảng stores
        $columns = Schema::getColumnListing('stores');
        $this->info('Store table columns:');
        print_r($columns);

        // Tạo test store
        $test = Store::create([
            'name' => 'Test Store Debug',
            'description' => 'Test description field',
            'about_store' => 'Test about_store field',
            'slug' => 'test-store-debug',
            'cat_id' => 0,
            'status' => 1,
            'user_id' => 1,
            'language' => 'en',
        ]);

        $this->info("\nCreated store ID: " . $test->id);
        $this->info("description: " . $test->description);
        $this->info("about_store: " . $test->about_store);

        // Xóa test
        $test->forceDelete();

        return 0;
    }
}
