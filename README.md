# WebCouponNew

Website coupon/deals xây dựng trên **Laravel 12** + **PHP 8.2+**, gồm frontend tin tức/store và admin CMS (`/backend`).

---

## Yêu cầu hosting

| Thành phần | Phiên bản tối thiểu |
|---|---|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Web server | Apache (mod_rewrite) hoặc Nginx |

**PHP extensions bắt buộc:**

- `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- `gd` (bắt buộc nếu dùng tạo bài viết AI — chuyển ảnh sang WebP)

**Khuyến nghị production:**

- `redis` hoặc bật cache/session qua database (mặc định project dùng `database`)
- SSL/HTTPS
- Cron job cho Laravel Scheduler

---

## Cấu trúc trên hosting

Document root **phải trỏ vào thư mục `public/`**, không trỏ vào root project.

```
/home/user/webcoupon/          ← root mã nguồn
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                    ← document root của domain
│   ├── index.php
│   ├── uploads/               ← ảnh upload (cần ghi)
│   └── ...
├── storage/                   ← cần ghi
├── vendor/
├── .env
└── artisan
```

Admin: `https://your-domain.com/backend`

---

## Deploy lần đầu

### 1. Upload mã nguồn

Upload toàn bộ project lên server (Git clone hoặc FTP), **trừ** `vendor/` nếu sẽ cài bằng Composer trên server.

### 2. Cài dependency PHP

```bash
cd /path/to/webcoupon

composer install --no-dev --optimize-autoloader
```

### 3. Tạo file `.env`

```bash
cp .env.example .env   # nếu có file mẫu
# hoặc tạo .env thủ công
php artisan key:generate
```

Cấu hình tối thiểu trong `.env`:

```env
APP_NAME=WebCoupon
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Asia/Ho_Chi_Minh

SITE_NAME=DealHunter365

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

PREFIX_ADMIN=backend

CKFINDER_LICENSE_NAME=your-domain.com
CKFINDER_LICENSE_KEY=your-ckfinder-key
```

> API Gemini / Apify cấu hình trong admin: **Cài đặt → Cấu hình AI** (lưu trong bảng `settings`, không cần ghi vào `.env`).

### 4. Tạo database & chạy migration

```bash
php artisan migrate --force
```

### 5. Seed dữ liệu ban đầu (tuỳ chọn)

```bash
# Settings + danh mục mặc định
php artisan db:seed --force

# Chỉ seed settings
php artisan db:seed --class=SettingsSeeder --force

# Chỉ seed danh mục
php artisan db:seed --class=CategoriesSeeder --force
```

### 6. Liên kết storage & phân quyền

```bash
php artisan storage:link

chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads
chown -R www-data:www-data storage bootstrap/cache public/uploads
```

> Trên cPanel/shared hosting: đặt quyền ghi cho `storage/`, `bootstrap/cache/`, `public/uploads/` qua File Manager.

### 7. Cache cấu hình production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

### 8. Cấu hình Cron (bắt buộc nếu dùng AI auto-post)

Thêm cron trên hosting (chạy mỗi phút):

```cron
* * * * * cd /path/to/webcoupon && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler đang chạy:

- `post:auto-generate` — tạo bài viết AI tự động (mỗi phút, tuỳ cấu hình admin)
- `store:capture-view-snapshot` — snapshot lượt xem store (23:55 hàng ngày)

**Lưu ý:** Không dùng `php artisan schedule:work` trên shared hosting. Chỉ dùng cron như trên.

---

## Deploy cập nhật (mỗi lần release)

```bash
cd /path/to/webcoupon

git pull origin main          # hoặc upload file mới

composer install --no-dev --optimize-autoloader

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Xóa cache frontend sau deploy lớn (tuỳ chọn)
php artisan cache:clear
```

---

## Lệnh Artisan thường dùng

### Import store / offer

```bash
# Tạo lại file Excel mẫu import store
php artisan import:sample-store
```

Nếu danh mục trong Excel không khớp DB → chạy lại:

```bash
php artisan db:seed --class=CategoriesSeeder --force
php artisan import:sample-store
```

Sau đó tải file mẫu mới tại admin (dropdown danh mục sẽ đúng).

### AI tạo bài viết

```bash
# Tạo bài tự động ngay (bỏ qua interval)
php artisan post:auto-generate --force
```

Bật/tắt và interval cấu hình tại admin: **Cài đặt → Cấu hình AI**.

### Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Kiểm tra hệ thống

```bash
php artisan about
php -m                          # liệt kê PHP extensions
php -r "echo function_exists('imagewebp') ? 'WebP OK' : 'WebP MISSING';"
```

---

## Build frontend (tuỳ chọn)

Frontend chính dùng asset tĩnh trong `public/css`, `public/js` — **không bắt buộc** chạy npm khi deploy.

Chỉ cần build Vite nếu bạn chỉnh layout auth (`resources/views/layouts/`):

```bash
npm ci
npm run build
```

---

## Checklist sau deploy

- [ ] Trang chủ load bình thường: `https://your-domain.com`
- [ ] Admin đăng nhập được: `https://your-domain.com/backend`
- [ ] Upload ảnh trong admin hoạt động (`public/uploads/` ghi được)
- [ ] CKFinder mở được (license đúng trong `.env`)
- [ ] Cron `schedule:run` đã cấu hình (nếu dùng AI auto-post)
- [ ] `APP_DEBUG=false` trên production
- [ ] HTTPS bật, `APP_URL` đúng domain

---

## Xử lý lỗi thường gặp

### 500 Internal Server Error

```bash
tail -f storage/logs/laravel.log
php artisan config:clear
php artisan cache:clear
```

Kiểm tra quyền ghi `storage/`, `bootstrap/cache/`.

### 404 mọi route (trừ trang chủ)

- Apache: bật `mod_rewrite`, document root trỏ `public/`
- Kiểm tra file `public/.htaccess` tồn tại

### Import store lỗi 500 / cat_id = 0

```bash
php artisan db:seed --class=CategoriesSeeder --force
php artisan import:sample-store
```

### Icon / CSS không load

Hard refresh (`Ctrl+F5`). Kiểm tra `public/vendor/fontawesome/` và `public/css/` đã upload đủ.

### Ảnh AI không chuyển WebP

Hosting cần PHP extension **GD** có hỗ trợ WebP:

```bash
php -r "echo function_exists('imagewebp') ? 'OK' : 'Missing';"
```

---

## Tham khảo

- Demo giao diện tham khảo: https://demos.ascendoor.com/ascendoor-news/contact-us/
- Laravel docs: https://laravel.com/docs

---

## License

MIT (Laravel framework)
