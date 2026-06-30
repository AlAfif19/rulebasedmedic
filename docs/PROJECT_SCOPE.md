# Scope Implementasi

Project ini dibuat berdasarkan kebutuhan sistem pakar rekomendasi obat berbasis web. Struktur data mengikuti rancangan entitas pengguna, gejala, penyakit, obat, rule, dan riwayat. Alur diagnosis menerapkan input gejala sebagai fakta awal, pencocokan rule, penarikan kesimpulan penyakit, rekomendasi obat, lalu penyimpanan riwayat.

## Metode

Semua metode berjalan paralel pada setiap konsultasi, bukan sebagai pilihan manual pengguna.

1. Rule Based: aturan disimpan pada tabel `rules` dalam bentuk `symptom_codes` dan `medicine_codes`.
2. Forward Chaining: sistem memulai dari gejala yang dipilih pengguna, lalu mencocokkan rule.
3. Backward Chaining: sistem memvalidasi kemungkinan penyakit dengan memeriksa gejala yang dibutuhkan rule.
4. Certainty Factor: sistem menghitung skor keyakinan dari nilai rule, bobot gejala, dan rasio kecocokan.

## Struktur Halaman

### Masyarakat

- Landingpage
- Login dan Register
- Beranda
- Cek Gejala
- Hasil Rekomendasi
- Riwayat Penggunaan Obat
- Informasi Obat
- Profil

### Admin Apoteker

- Dashboard
- Data Gejala
- Data Penyakit
- Data Obat
- Data Rule
- Data User
- Riwayat
- Pengaturan
- Logout
