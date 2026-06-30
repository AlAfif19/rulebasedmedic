# Verifikasi

Tanggal verifikasi: 2026-06-30

## Perintah Yang Berhasil

```bash
composer dump-autoload --no-interaction
php artisan test --filter=ExpertSystemServiceTest
php artisan test --filter=ConsultationFlowTest
php artisan test --filter=RoleAccessTest
php artisan test --filter=AdminResourceTest
php artisan test --filter=ContactAndInterfaceTest
php artisan test
npm run build
```

Hasil:

- ExpertSystemServiceTest: 4 test, 21 assertion, lulus.
- ConsultationFlowTest: 1 test, 10 assertion, lulus.
- RoleAccessTest: 2 test, 3 assertion, lulus.
- AdminResourceTest: 3 test, 14 assertion, lulus. Termasuk verifikasi upload gambar obat dikompres menjadi WebP, ukuran file kecil, dan dimensi maksimum 900 px.
- ContactAndInterfaceTest: 6 test, 39 assertion, lulus.
- Full test suite: 16 test, 87 assertion, lulus.
- Vite production build berhasil menghasilkan asset di `public/build`.

## Perbaikan Infrastruktur Verifikasi

- Menambahkan `phpunit.xml.dist` untuk test SQLite in-memory.
- Menambahkan `mockery/mockery` ke dependency dev karena Laravel testing membutuhkannya.
- Memperbaiki `config/app.php` agar default service provider Laravel dimuat bersama provider aplikasi.
- Test memanggil `withoutVite()` agar render Blade tidak bergantung pada manifest build saat testing.

## Catatan Audit Dependency

`composer update mockery/mockery --with-dependencies` melaporkan 3 security advisory pada 1 package transitive. `npm install` melaporkan 2 vulnerability. Belum dijalankan auto-fix paksa karena dapat menaikkan versi mayor dan berisiko mengubah kompatibilitas Laravel/Vite.

## Run Lokal

Cara menjalankan:

```bash
bash start.sh
```

Script menyiapkan database MySQL `rulebasedmedic` dengan user `root` tanpa password, menjalankan migrasi dan seeder, lalu menyalakan Laravel serta Vite dev server.

Smoke test pada environment ini:

```bash
"C:\Program Files\Git\bin\bash.exe" start.sh
php artisan test
npm run build
```

Hasil:

- Git Bash tersedia pada `C:\Program Files\Git\bin\bash.exe`.
- `start.sh` berhasil mendeteksi MySQL client XAMPP di `/c/xampp/mysql/bin/mysql.exe`.
- Database `rulebasedmedic` dibuat otomatis jika belum ada.
- Laravel berjalan di `http://127.0.0.1:8000`.
- Vite dev server berjalan di `http://127.0.0.1:5173`.
- Halaman `/`, `/login`, `/login?admin=1`, dan `/informasi` mengembalikan status 200.
- `php artisan test` lulus dengan 16 test dan 87 assertion.
- `npm run build` berhasil menghasilkan asset production di `public/build`.
- Halaman `/informasi` memuat data Apotek Bhakti Medika Farma, link Google Maps, Instagram `@bhaktimedikafarma`, asset `medical-hero.svg`, pencarian obat, dan embed OpenStreetMap.

Cara menghentikan:

```bash
bash stop.sh
```
