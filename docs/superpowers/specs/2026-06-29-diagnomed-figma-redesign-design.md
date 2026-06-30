# DiagnoMed Figma Redesign Design

Tanggal: 2026-06-29

## Tujuan

Mengubah project Laravel RuleBasedMedic menjadi aplikasi DiagnoMed yang mengikuti UI Figma dan PDF referensi: sistem pakar rekomendasi obat berbasis web untuk masyarakat dan admin apoteker. Backend yang sudah ada dipertahankan sejauh mungkin: autentikasi, role, CRUD resource, data 100 gejala, 100 obat, 50 rule, serta inferensi Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor.

## Prinsip Produk

Sistem bersifat edukatif dan informatif, bukan diagnosis medis. Rekomendasi obat harus selalu disertai peringatan swamedikasi, batas 3 x 24 jam, serta anjuran konsultasi ke dokter atau apoteker untuk gejala berat, alergi, hamil/menyusui, anak, lansia, penyakit kronis, atau keluhan yang memburuk.

Tidak ada emotikon di kode, seed data, maupun teks UI. Ikon memakai SVG atau komponen Blade berbasis icon sederhana.

## Target Visual

Identitas aplikasi memakai nama DiagnoMed dengan subtitle "Sistem Rekomendasi Obat". Tampilan mengikuti screenshot Figma:

- Warna utama: gradient biru medis `#1f5d95` ke `#2d91e6`.
- Background halaman: biru sangat muda `#f2f6fc` atau `#f5f8fd`.
- Surface: putih dengan border `#dce5f1`, shadow ringan, radius 6-10px.
- Aksen: hijau `#067a42` untuk tombol riwayat/status aman, biru `#2385dd` untuk aksi utama, merah `#ff4d4f` untuk hapus/peringatan, pastel kategori untuk badge.
- Layout desktop memakai frame tengah lebar seperti Figma, dengan konten dense dan rapi. Mobile memakai susunan satu kolom, sticky/compact navigation, tabel menjadi card atau horizontal scroll.
- Ilustrasi medis memakai asset ringan terkompresi: obat, botol, clipboard rekomendasi, dan tanaman seperti Figma.

## Halaman Masyarakat

### Login dan Registrasi

Login dan registrasi memakai split layout:

- Panel kiri: gradient biru tinggi penuh, headline "Selamat Datang Kembali" atau "Selamat Datang", ilustrasi medis, deskripsi "Sistem Rekomendasi Obat", dan indicator dot.
- Panel kanan: logo DiagnoMed, form input dengan icon kecil, checkbox "Ingat Saya", link lupa password pada login, link pindah login/register.
- Admin login memakai desain khusus: background gradient biru full screen dengan card login putih di tengah.

### Homepage/Beranda

Homepage memakai navbar putih: logo, menu Beranda, Cek Gejala, Riwayat, Informasi, search pill, icon notifikasi, login/register atau user dropdown.

Konten utama:

- Hero banner biru dengan judul "Sistem Rekomendasi Obat Berdasarkan Penyakit Ringan", copy pendek, ilustrasi medis di kanan, tombol "Mulai Cek Gejala" dan "Lihat Riwayat".
- Empat benefit: Diagnosa Akurat, Rekomendasi Akurat, Aman & Terpercaya, Riwayat Tersimpan.
- Bagian "Bagaimana Cara Kerjanya?" dengan kartu proses: Pilih Gejala, Proses Analisis, Lihat Hasil, Rekomendasi Obat.
- Disclaimer bawah bahwa sistem hanya memberi informasi.

### Cek Gejala

Halaman mengikuti Figma:

- Banner biru "Cek Gejala".
- Stepper 4 tahap: Pilih, Konfirmasi, Proses, Hasil.
- Panel kiri berisi search gejala, tab kategori, checkbox gejala, badge kategori, pagination ringan.
- Panel kanan berisi "Gejala yang dipilih", tombol bersihkan, daftar selected symptoms, tips pengisian, dan tombol "Selanjutnya".
- Form tetap mengirim ke endpoint konsultasi existing. Method pilihan tetap mendukung forward, backward, dan certainty bila tersedia di UI.

### Hasil Rekomendasi dan Detail Obat

Halaman hasil memakai dua kolom desktop:

- Kiri: gejala yang dipilih, hasil diagnosa penyakit/ranking, severity badge, confidence/certainty factor.
- Kanan: rekomendasi obat dengan image kecil, nama, dosis, aturan pakai ringkas, tombol "Detail Obat".
- Bawah: saran dan anjuran, termasuk batas 3 x 24 jam dan peringatan bukan diagnosis medis.

Detail obat dibuka sebagai modal besar:

- Header dropdown "Obat Lainnya" dan tombol tutup.
- Kiri: gambar obat ringan dari asset terkompresi atau kartu gambar generik bergaya Figma.
- Kanan: nama obat, dosis, harga bila tersedia, tab Tentang, Aturan, Efek, Peringatan, Interaksi, serta bentuk/produsen bila tersedia.

### Riwayat dan Informasi

Riwayat memakai:

- Banner biru "Riwayat Penggunaan Obat".
- Kartu ringkasan total diagnosis, penyakit teridentifikasi, obat direkomendasikan, riwayat terakhir.
- Filter tanggal, penyakit, urutan, search, reset.
- Tabel riwayat dan panel "Detail Riwayat Terakhir".

Informasi berisi edukasi obat, aturan pakai, efek samping, klasifikasi obat, kapan harus ke dokter, kontak WA, Instagram, Facebook, lokasi, mini maps, dan footer.

## Halaman Admin Apoteker

Admin memakai layout Figma:

- Sidebar biru solid full height, logo kecil, menu Dashboard, Data Gejala, Data Penyakit, Data Obat, Data Rule, Data User, Riwayat, Pengaturan, Logout.
- Profile picture admin berada di bagian bawah sidebar.
- Header kanan berisi search pill, notifikasi, kalender/tanggal.

Dashboard:

- Banner "Selamat datang, Admin!".
- Kartu statistik total gejala, penyakit, obat, rule, user, riwayat.
- Grafik statistik diagnosis 7 hari terakhir.
- Donut distribusi tingkat keparahan.
- Tabel riwayat diagnosa terbaru.
- Aktivitas sistem terbaru.

CRUD:

- Semua resource memakai card tabel putih seperti Figma.
- Header tabel memiliki info alert, tombol tambah, search, filter kategori/lokasi/status, reset.
- Tabel gejala, penyakit, obat, rule, user, riwayat, dan pengaturan memakai badge kategori/severity dan icon action edit/hapus.
- Form create/edit tetap sederhana, responsive, dan mengikuti gaya input Figma.

## Backend dan Data

Backend existing tetap menjadi dasar:

- `ExpertSystemService` dipakai untuk analisis forward/backward/certainty.
- `ConsultationController` menyimpan riwayat.
- `ResourceController` tetap menjadi entry CRUD bila cukup, dengan penyesuaian UI dan field.
- Seeder `database/data/medical.php` tetap menjadi basis knowledge.

Tambahan yang boleh dilakukan bila dibutuhkan:

- Field profil pengguna: jenis kelamin, tanggal lahir, status hamil/menyusui, nomor HP, alamat, profile picture.
- Field obat detail: bentuk, produsen, harga, gambar, interaksi.
- Helper atau view component untuk logo, icon, banner, stepper, stat card, badge, data table, dan modal.

Database menggunakan MySQL user `root` tanpa password. `start.sh` dan `stop.sh` tetap menjadi cara menjalankan aplikasi via Git Bash, tanpa Docker.

## Responsiveness

Desain mobile first:

- Navbar menjadi compact/hamburger pada mobile.
- Split login menjadi stacked: panel ilustrasi pendek di atas, form di bawah.
- Cek gejala menjadi satu kolom: selected symptoms muncul sebelum tombol submit atau sebagai sticky bottom summary.
- Tabel admin memakai horizontal scroll atau card list pada mobile.
- Sidebar admin menjadi drawer pada tablet/mobile.
- Tidak ada teks yang keluar dari container; tombol minimal 40px hit area.

## Asset dan Performa

Asset gambar harus ringan. SVG existing boleh dipakai bila ukurannya kecil. Raster image baru harus dikompresi sebelum masuk `public/assets`. Tidak menggunakan video kecuali benar-benar diperlukan; bila ada, harus dikompresi.

Animasi mengikuti Motion Dev/Magic UI/Aeternity UI secara pragmatis dengan CSS/JS ringan di Blade:

- Fade/slide subtle untuk section.
- Hover glow ringan pada card.
- Active scale `0.96` pada tombol.
- Tidak memakai animasi berat yang mengganggu form dan tabel.

## Testing dan Verifikasi

Implementasi memakai TDD untuk perubahan behavior:

- Test inferensi forward menghasilkan penyakit/obat yang tepat.
- Test backward/certainty menghasilkan confidence dan ranked rules.
- Test role masyarakat tidak bisa mengakses admin, admin tidak diarahkan ke halaman masyarakat.
- Test konsultasi menyimpan riwayat.
- Test CRUD resource dasar bila controller diubah.

Verifikasi akhir:

- `composer test` atau `php artisan test`.
- `npm run build`.
- `php artisan migrate:fresh --seed` pada database lokal bila dependency tersedia.
- Manual check via `bash start.sh` bila environment mendukung.

## Catatan Batasan

Link Figma tidak dapat dibaca langsung oleh environment agent, tetapi screenshot pengguna dan render halaman UI dari PDF digunakan sebagai referensi visual utama. Project saat ini bukan git repository lengkap karena folder `.git` tidak ada, sehingga commit desain tidak dapat dilakukan dari workspace ini.
