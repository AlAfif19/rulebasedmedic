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
- `osm_embed_url`

Setelah mengubah data di seeder, jalankan:

```bash
php artisan db:seed
```

Jika ingin mengubah lewat database langsung, edit tabel `app_settings`.

Contoh update cepat lewat Tinker:

```bash
php artisan tinker
```

```php
\App\Models\AppSetting::updateOrCreate(
    ['key' => 'social_instagram'],
    ['value' => 'nama_instagram_baru', 'group' => 'social']
);

\App\Models\AppSetting::updateOrCreate(
    ['key' => 'opening_hours'],
    ['value' => 'Senin - Sabtu, 08.00 - 20.00. Minggu tutup.', 'group' => 'contact']
);
```

Setelah mengganti data:

```bash
php artisan cache:clear
php artisan view:clear
```

Tampilan footer dan halaman informasi mengambil data dari tabel `app_settings`, jadi programmer baru tidak perlu mengedit Blade hanya untuk mengganti nama tempat, alamat, jam buka, WhatsApp, Instagram, Facebook, plus code, link Google Maps, atau embed peta.

Catatan maps:

- `maps_url` dipakai untuk tombol `Buka Google Maps`.
- `osm_embed_url` dipakai untuk thumbnail peta yang tampil di halaman dan footer.
- Jika thumbnail tidak tampil, ganti `osm_embed_url` dengan URL embed OpenStreetMap baru.

## 9. Cara Mengganti Asset

Folder asset utama:

```text
public/assets/images/
```

Asset yang dipakai:

- `logo.svg`: logo aplikasi.
- `medical-hero.svg`: gambar utama yang Anda kirim, berisi obat, botol, clipboard rekomendasi, dan tanaman. File ini dipakai di login, register, landing page, dan banner halaman.
- `medicine-box.svg`: gambar kemasan obat untuk preview dan katalog.

Cara mengganti asset:

1. Siapkan gambar baru.
2. Kompres gambar terlebih dahulu jika file dimasukkan manual ke folder asset.
3. Simpan ke `public/assets/images/`.
4. Gunakan nama file yang sama jika ingin mengganti langsung.
5. Jika nama file berbeda, update pemanggilan di Blade component atau data database.

Jika ingin mengganti gambar utama seperti contoh Figma, ganti file ini:

```text
public/assets/images/medical-hero.svg
```

Tempat pemakaian gambar utama:

```text
resources/views/components/diagnomed/hero-banner.blade.php
resources/views/auth/login.blade.php
resources/views/auth/register.blade.php
resources/views/landing.blade.php
```

Jika filenya diganti menjadi PNG atau WebP, ubah `medical-hero.svg` di file-file tersebut menjadi nama file baru, misalnya:

```blade
{{ asset('assets/images/medical-hero.webp') }}
```

Rekomendasi ukuran:

- SVG untuk logo dan ilustrasi UI.
- WebP untuk foto obat dan gambar raster.
- Hindari gambar besar di atas 500 KB untuk UI reguler.
- Untuk banner/hero raster, gunakan lebar sekitar 1200 px.
- Untuk thumbnail obat, gunakan lebar sekitar 600-900 px.

Catatan kompresi:

- Upload gambar obat dari admin otomatis dikompres oleh sistem.
- File `jpg`, `jpeg`, `png`, dan `webp` akan disimpan ulang menjadi WebP kualitas ringan dengan dimensi maksimum 900 px.
- File `svg` tidak dikonversi karena SVG adalah vector dan biasanya kecil.
- Asset yang diganti manual langsung di `public/assets/images/` tetap perlu dikompres sebelum disimpan.

## 10. Cara Menambah Gambar Obat di Database

Kolom gambar obat adalah:

```text
medicines.image_path
```

Contoh nilai:

```text
assets/images/paracetamol.webp
```

Langkah lewat admin:

1. Login admin.
2. Buka `Data Obat`.
3. Klik edit pada obat.
4. Pada bagian `Image Path`, klik area upload atau tarik file gambar ke area tersebut.
5. Simpan.

File upload akan disimpan otomatis ke:

```text
public/assets/uploads/medicines/
```

Gambar raster yang diupload akan otomatis dikompres menjadi WebP agar web tidak berat.
Kolom `image_path` akan diisi otomatis, misalnya:

```text
assets/uploads/medicines/paracetamol-1234567890.webp
```

Aturan upload gambar obat:

- Format yang diterima: `jpg`, `jpeg`, `png`, `webp`, dan `svg`.
- Ukuran file awal maksimal 1 MB.
- Gambar raster otomatis diubah ke WebP.
- Dimensi gambar raster otomatis dibatasi maksimal 900 px pada sisi terpanjang.
- Jika ingin menjaga background transparan, gunakan PNG/WebP transparan atau SVG.

Langkah manual lewat database:

1. Simpan file gambar ke `public/assets/images/`.
2. Isi field `image_path` dengan path gambar.

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

Folder utama:

```text
app/Services/
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

### Mengapa Skor Diagnosis Bisa Kecil

Skor kecil adalah normal jika gejala yang dipilih hanya cocok sebagian dengan rule.

Contoh:

- User memilih `G007`, `G010`, `G012`.
- Rule terbaik `R004` membutuhkan `G009`, `G010`, `G062`.
- Yang cocok hanya `G010`.
- Kecocokan rule menjadi sekitar `33.33%`.
- Skor paralel menjadi sekitar `28%`.

Artinya hasil tersebut adalah kemungkinan awal, bukan kecocokan kuat. User sebaiknya menambah gejala yang benar-benar dialami agar sistem dapat menemukan rule yang lebih tepat.

Pada halaman hasil, sistem menampilkan:

- rule yang cocok,
- persentase kecocokan,
- gejala yang cocok,
- gejala rule yang belum dipilih,
- peringatan jika skor rendah.

### Lokasi Coding Metode Forward, Backward, dan Certainty Factor

Tiga metode inferensi utama disimpan dalam satu service:

```text
Folder: app/Services/
File: app/Services/ExpertSystemService.php
Class: App\Services\ExpertSystemService
```

Rincian lokasi di file:

- Forward Chaining:
  Disimpan di `app/Services/ExpertSystemService.php`, terutama pada method `calculateMethodScores()`.
  Skor forward dihitung dari kelengkapan rule dan nilai `cf_value` rule, lalu disimpan sebagai key `forward_chaining`.

- Backward Chaining:
  Disimpan di `app/Services/ExpertSystemService.php`, pada method `calculateMethodScores()` untuk skor otomatis dan method `backwardCheck()` untuk pengecekan goal penyakit tertentu.
  Skor backward disimpan sebagai key `backward_chaining`.

- Certainty Factor:
  Disimpan di `app/Services/ExpertSystemService.php`, pada method `calculateCertaintyFactor()`.
  Nilainya dihitung dari `cf_value` rule, bobot gejala, dan rasio kecocokan gejala, lalu disimpan sebagai key `certainty_factor`.

Method pendukung:

```text
analyze()
calculateMethodScores()
calculateCertaintyFactor()
calculateParallelScore()
scoreForMethod()
backwardCheck()
```

File yang memanggil service saat user melakukan konsultasi:

```text
Folder: app/Http/Controllers/
File: app/Http/Controllers/ConsultationController.php
Method: diagnose()
```

Di `ConsultationController::diagnose()`, hasil dari `ExpertSystemService::analyze()` disimpan ke tabel riwayat konsultasi melalui model:

```text
app/Models/Consultation.php
```

Data skor metode tersimpan di kolom `result_payload` dengan struktur:

```text
result_payload.method_scores.rule_based
result_payload.method_scores.forward_chaining
result_payload.method_scores.backward_chaining
result_payload.method_scores.certainty_factor
result_payload.matched_rule.parallel_score
```

## 13. Cara Mencari Obat di Halaman Informasi

Halaman `Informasi` memiliki pencarian obat berdasarkan:

- nama obat,
- kode obat,
- kategori,
- deskripsi.

Contoh URL pencarian:

```text
http://127.0.0.1:8000/informasi?q=Becom
```

Jika hasil tidak ditemukan, sistem menampilkan pesan kosong dan user bisa menekan `Reset`.

## 14. Struktur Komponen UI

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

## 15. File Controller Penting

- `app/Http/Controllers/AuthController.php`: login, register, logout.
- `app/Http/Controllers/ConsultationController.php`: cek gejala, simpan hasil, riwayat, detail hasil.
- `app/Http/Controllers/HomeController.php`: landing, dashboard masyarakat, informasi obat.
- `app/Http/Controllers/Admin/DashboardController.php`: analytic admin.
- `app/Http/Controllers/Admin/ResourceController.php`: CRUD admin.

## 16. Menjalankan Test

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

## 17. Troubleshooting

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

## 18. Alur Programmer Baru

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

## 19. Catatan Keamanan Medis

Sistem ini bersifat edukatif dan informatif. Rekomendasi obat bukan pengganti diagnosis dokter. Jika gejala berat, alergi, hamil atau menyusui, memiliki penyakit kronis, atau keluhan tidak membaik dalam 3 x 24 jam, pengguna perlu diarahkan ke dokter atau apoteker.
