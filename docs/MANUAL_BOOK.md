# Manual Book DiagnoMed RuleBasedMedic

Dokumen ini menjelaskan cara menjalankan aplikasi, memakai fitur user dan admin, mengganti data, mengganti asset, serta merawat project Laravel DiagnoMed RuleBasedMedic.

## 1. Ringkasan Aplikasi

DiagnoMed RuleBasedMedic adalah sistem pakar rekomendasi obat berbasis Laravel. Sistem menerima gejala dari masyarakat, menjalankan metode Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor secara paralel, lalu menampilkan kemungkinan penyakit ringan beserta rekomendasi obat.

Role aplikasi:

- Masyarakat: melihat beranda, cek gejala, melihat hasil rekomendasi, riwayat, informasi obat, profil, dan logout.
- Admin Apoteker: dashboard, analytic, CRUD gejala, penyakit, obat, rule, user, riwayat, pengaturan, dan logout.

## 2. Kebutuhan Sistem

Pastikan perangkat memiliki:

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan NPM
- MySQL aktif dengan user `root` tanpa password
- Git Bash di Windows
- Browser modern

Project tidak menggunakan Docker.

## 3. Cara Menjalankan Program

Masuk ke folder project:

```bash
cd rulebasedmedic
```

Jalankan aplikasi:

```bash
bash start.sh
```

Script akan:

- Membuat file `.env` dari `.env.example` jika belum ada.
- Menginstall dependency Composer jika `vendor` belum ada.
- Menginstall dependency NPM jika `node_modules` belum ada.
- Membuat database MySQL `rulebasedmedic` jika belum ada.
- Menjalankan migration dan seeder.
- Menjalankan Laravel server di `http://127.0.0.1:8000`.
- Menjalankan Vite dev server di `http://127.0.0.1:5173`.

Buka aplikasi:

```text
http://127.0.0.1:8000
```

Menghentikan aplikasi:

```bash
bash stop.sh
```

## 4. Akun Awal

Admin Apoteker:

```text
username: admin
password: password
```

Masyarakat:

```text
username: masyarakat
password: password
```

## 5. Cara Mengganti Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rulebasedmedic
DB_USERNAME=root
DB_PASSWORD=
```

Jika ingin memakai nama database lain, ganti `DB_DATABASE`, lalu jalankan:

```bash
php artisan migrate:fresh --seed
```

Perintah tersebut akan menghapus seluruh tabel di database aktif, membuat ulang tabel, dan mengisi data awal.

## 6. Cara Menghapus Riwayat

Lewat Admin:

1. Login sebagai admin.
2. Buka menu `Riwayat`.
3. Klik tombol hapus pada data yang ingin dihapus.

Lewat terminal:

```bash
php artisan tinker
```

```php
\App\Models\Consultation::truncate();
```

Jika memakai foreign key MySQL dan truncate tertahan, gunakan:

```php
\App\Models\Consultation::query()->delete();
```

## 7. Cara Menghapus atau Menambah User

Lewat Admin:

1. Login sebagai admin.
2. Buka menu `Data User`.
3. Klik `Tambah User` untuk menambah user.
4. Klik ikon edit untuk mengubah data user.
5. Klik ikon hapus untuk menghapus user.

Lewat seeder:

- File akun awal ada di `database/seeders/DatabaseSeeder.php`.
- Setelah mengubah seeder, jalankan:

```bash
php artisan db:seed
```

## 8. Cara Mengganti Informasi Apotek

Data apotek awal ada di `database/seeders/DatabaseSeeder.php`, bagian `AppSetting`.

Data yang tersedia:

- `pharmacy_name`
- `contact_whatsapp`
- `contact_phone_display`
- `social_instagram`
- `social_facebook`
- `location`
- `opening_hours`
- `maps_plus_code`
- `maps_url`

Setelah mengubah data di seeder, jalankan:

```bash
php artisan db:seed
```

Jika ingin mengubah lewat database langsung, edit tabel `app_settings`.

## 9. Cara Mengganti Asset

Folder asset utama:

```text
public/assets/images/
```

Asset yang dipakai:

- `logo.svg`: logo aplikasi.
- `medical-hero.svg`: gambar banner obat, botol, clipboard, dan tanaman.
- `medicine-box.svg`: gambar kemasan obat untuk preview dan katalog.

Cara mengganti asset:

1. Siapkan gambar baru.
2. Kompres gambar terlebih dahulu.
3. Simpan ke `public/assets/images/`.
4. Gunakan nama file yang sama jika ingin mengganti langsung.
5. Jika nama file berbeda, update pemanggilan di Blade component atau data database.

Rekomendasi ukuran:

- SVG untuk logo dan ilustrasi UI.
- WebP atau PNG terkompresi untuk foto obat.
- Hindari gambar besar di atas 500 KB untuk UI reguler.

## 10. Cara Menambah Gambar Obat di Database

Kolom gambar obat adalah:

```text
medicines.image_path
```

Contoh nilai:

```text
assets/images/paracetamol.webp
```

Langkah:

1. Simpan file gambar ke `public/assets/images/`.
2. Login admin.
3. Buka `Data Obat`.
4. Edit obat.
5. Isi field `image_path` jika tersedia di form, atau update langsung lewat database.

Contoh lewat Tinker:

```bash
php artisan tinker
```

```php
\App\Models\Medicine::where('code', 'O001')->update([
    'image_path' => 'assets/images/paracetamol.webp',
]);
```

## 11. Cara Mengelola Data Pakar

Menu admin:

- `Data Gejala`: mengelola kode, nama, kategori, bobot, lokasi tubuh, dan status gejala.
- `Data Penyakit`: mengelola kode penyakit, nama, keparahan, deskripsi, dan solusi.
- `Data Obat`: mengelola kode obat, nama, dosis, aturan pakai, efek samping, kontraindikasi, peringatan, dan gambar.
- `Data Rule`: mengelola IF gejala, THEN penyakit, output obat, nilai CF, dan status rule.

Format kode gejala dan obat pada rule dipisahkan koma:

```text
G001, G009, G011
```

```text
O001, O002, O003
```

Metode pada rule disimpan sebagai `parallel`. User tidak memilih metode secara manual.

## 12. Cara Kerja Metode Paralel

File utama:

```text
app/Services/ExpertSystemService.php
```

Alur:

1. User memilih gejala.
2. Sistem mengambil rule aktif.
3. Setiap rule dihitung dengan:
   - Rule Based
   - Forward Chaining
   - Backward Chaining
   - Certainty Factor
4. Skor digabung menjadi `parallel_score`.
5. Rule dengan skor terbaik dipilih sebagai hasil utama.
6. Obat dari rule tersebut ditampilkan dan riwayat disimpan.

Payload riwayat menyimpan:

- `disease`
- `medicines`
- `method_scores`
- `matched_rule`
- `parallel_score`

## 13. Struktur Komponen UI

Komponen Blade ada di:

```text
resources/views/components/diagnomed/
```

Komponen penting:

- `logo.blade.php`: logo DiagnoMed.
- `icon.blade.php`: ikon SVG internal.
- `hero-banner.blade.php`: banner biru halaman.
- `medicine-art.blade.php`: visual obat atau gambar dari `image_path`.
- `badge.blade.php`: label kategori/status.
- `stepper.blade.php`: langkah cek gejala.
- `pagination.blade.php`: tombol previous, nomor halaman, dan next.

Layout:

- `resources/views/layouts/app.blade.php`: layout masyarakat.
- `resources/views/layouts/admin.blade.php`: layout admin.

Partial:

- `resources/views/partials/navbar.blade.php`
- `resources/views/partials/footer.blade.php`

## 14. File Controller Penting

- `app/Http/Controllers/AuthController.php`: login, register, logout.
- `app/Http/Controllers/ConsultationController.php`: cek gejala, simpan hasil, riwayat, detail hasil.
- `app/Http/Controllers/HomeController.php`: landing, dashboard masyarakat, informasi obat.
- `app/Http/Controllers/Admin/DashboardController.php`: analytic admin.
- `app/Http/Controllers/Admin/ResourceController.php`: CRUD admin.

## 15. Menjalankan Test

Jalankan semua test:

```bash
php artisan test
```

Jalankan test tertentu:

```bash
php artisan test --filter=ExpertSystemServiceTest
```

Build frontend:

```bash
npm run build
```

## 16. Troubleshooting

Jika database tidak ditemukan:

```bash
php artisan migrate --seed
```

Jika perubahan CSS tidak muncul:

```bash
npm run build
```

Jika server lama masih menyala:

```bash
bash stop.sh
bash start.sh
```

Jika cache view bermasalah:

```bash
php artisan view:clear
php artisan cache:clear
```

Jika ingin reset total data:

```bash
php artisan migrate:fresh --seed
```

## 17. Alur Programmer Baru

1. Clone repository.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Copy `.env.example` menjadi `.env`.
5. Jalankan `php artisan key:generate`.
6. Pastikan MySQL aktif.
7. Jalankan `php artisan migrate --seed`.
8. Jalankan `bash start.sh`.
9. Buka `http://127.0.0.1:8000`.
10. Sebelum push, jalankan `php artisan test` dan `npm run build`.

## 18. Catatan Keamanan Medis

Sistem ini bersifat edukatif dan informatif. Rekomendasi obat bukan pengganti diagnosis dokter. Jika gejala berat, alergi, hamil atau menyusui, memiliki penyakit kronis, atau keluhan tidak membaik dalam 3 x 24 jam, pengguna perlu diarahkan ke dokter atau apoteker.
