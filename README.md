# DiagnoMed RuleBasedMedic

DiagnoMed RuleBasedMedic adalah project Laravel untuk sistem pakar rekomendasi obat berbasis web dengan role Masyarakat dan Admin Apoteker. UI mengikuti referensi Figma/PDF DiagnoMed dengan navbar putih, banner biru, halaman cek gejala bertahap, hasil rekomendasi, riwayat, dashboard admin, sidebar biru, dan CRUD data pakar.

## Pengenalan Sistem

DiagnoMed membantu masyarakat melakukan pengecekan awal penyakit ringan berdasarkan gejala yang dipilih. Sistem menjalankan basis pengetahuan apoteker melalui Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor, lalu memberikan hasil berupa kemungkinan penyakit, tingkat keyakinan, riwayat konsultasi, serta rekomendasi obat yang dilengkapi dosis, peringatan, harga, dan satuan harga.

Admin Apoteker dapat mengelola data gejala, penyakit, obat, rule, user, riwayat konsultasi, dan pengaturan kontak apotek melalui dashboard khusus. Sistem ini dibuat untuk kebutuhan edukasi dan pendampingan swamedikasi, bukan sebagai pengganti diagnosis dokter.

## Screenshot Sistem

### Pengenalan Sistem

![Tampilan pengenalan sistem DiagnoMed](/docs/screenshots/landing.png)

### Alur Masyarakat

1. Login atau registrasi akun masyarakat.

![Halaman login masyarakat DiagnoMed](/docs/screenshots/login.png)

2. Masuk ke beranda masyarakat untuk melihat ringkasan sistem dan mulai cek gejala.

![Beranda masyarakat DiagnoMed](/docs/screenshots/masyarakat-dashboard.png)

3. Buka menu `Cek Gejala`, lalu pilih gejala yang sedang dialami.

![Halaman cek gejala DiagnoMed](/docs/screenshots/masyarakat-cek-gejala.png)

4. Sistem menampilkan gejala yang dipilih sebelum proses diagnosis.

![Gejala dipilih masyarakat DiagnoMed](/docs/screenshots/masyarakat-pilih-gejala.png)

5. Sistem menghitung hasil dengan Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor, lalu menampilkan diagnosis serta rekomendasi obat.

![Hasil diagnosis masyarakat DiagnoMed](/docs/screenshots/masyarakat-hasil.png)

6. Riwayat konsultasi tersimpan dan dapat dibuka kembali.

![Riwayat masyarakat DiagnoMed](/docs/screenshots/masyarakat-riwayat.png)

7. Masyarakat juga dapat membuka halaman informasi obat edukatif.

![Informasi obat DiagnoMed](/docs/screenshots/informasi.png)

### Tampilan Admin Apoteker

Admin Apoteker mengelola basis pengetahuan sistem melalui dashboard dan CRUD.

![Dashboard admin DiagnoMed](/docs/screenshots/admin-dashboard.png)

![Data rule admin DiagnoMed](/docs/screenshots/admin-rule.png)

![Data obat admin DiagnoMed](/docs/screenshots/admin-obat.png)

## Fitur Utama

- Role Masyarakat: login, register, landingpage, navbar, beranda, cek gejala, hasil rekomendasi, riwayat, informasi, profil, footer kontak, media sosial, lokasi, mini maps.
- Role Admin Apoteker: dashboard, sidebar, analytic ringkas, CRUD data gejala, data penyakit, data obat, data rule, data user, riwayat, pengaturan, logout, profile picture di bawah sidebar.
- Metode inferensi berjalan paralel: Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor dihitung bersama tanpa pilihan manual dari pengguna.
- Data awal: 100 gejala, 100 obat, 50 rule, dan penyakit dari matriks keterkaitan PDF.
- Data obat memiliki harga dan satuan harga, misalnya per butir, per strip, per sachet, per botol, per tube, atau per gram.
- UI responsive mobile first dengan Tailwind CSS dan komponen Blade bergaya shadcn, Magic UI, dan Aeternity UI.
- Tampilan DiagnoMed: login/register split layout, homepage Figma, cek gejala, hasil diagnosis dengan detail obat, riwayat, admin analytic, dan CRUD tabel.
- Tidak menggunakan Docker.

## Kebutuhan Lokal

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan NPM
- MySQL aktif dengan user root tanpa password
- Git Bash di Windows

## Cara Menjalankan

```bash
cd rulebasedmedic
bash start.sh
```

Akses:

```text
http://127.0.0.1:8000
```

Vite dev server dijalankan otomatis oleh `start.sh` pada:

```text
http://127.0.0.1:5173
```

## Akun Lokal

Kredensial admin dan masyarakat tidak ditampilkan di README agar tidak ikut
terpublikasi ke GitHub. Untuk development lokal, buat atau reset akun melalui
database seeder/internal tooling proyek, lalu simpan detail login di `.env` atau
catatan lokal yang tidak di-commit.

Menghentikan server:

```bash
bash stop.sh
```

## Manual Book

Panduan lengkap untuk user, admin, dan programmer baru tersedia di:

```text
docs/MANUAL_BOOK.md
```

## Catatan Medis

Sistem ini bersifat edukatif dan informatif. Rekomendasi obat bukan pengganti diagnosis dokter. Jika gejala berat, alergi, hamil/menyusui, penyakit kronis, atau keluhan tidak membaik dalam 3 x 24 jam, pengguna perlu diarahkan ke dokter atau apoteker.
