# DiagnoMed RuleBasedMedic

DiagnoMed RuleBasedMedic adalah project Laravel untuk sistem pakar rekomendasi obat berbasis web dengan role Masyarakat dan Admin Apoteker. UI mengikuti referensi Figma/PDF DiagnoMed dengan navbar putih, banner biru, halaman cek gejala bertahap, hasil rekomendasi, riwayat, dashboard admin, sidebar biru, dan CRUD data pakar.

## Fitur Utama

- Role Masyarakat: login, register, landingpage, navbar, beranda, cek gejala, hasil rekomendasi, riwayat, informasi, profil, footer kontak, media sosial, lokasi, mini maps.
- Role Admin Apoteker: dashboard, sidebar, analytic ringkas, CRUD data gejala, data penyakit, data obat, data rule, data user, riwayat, pengaturan, logout, profile picture di bawah sidebar.
- Metode inferensi berjalan paralel: Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor dihitung bersama tanpa pilihan manual dari pengguna.
- Data awal: 100 gejala, 100 obat, 50 rule, dan penyakit dari matriks keterkaitan PDF.
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

Akun awal:

```text
Admin Apoteker
username: admin
password: password

Masyarakat
username: masyarakat
password: password
```

Menghentikan server:

```bash
bash stop.sh
```

## Catatan Medis

Sistem ini bersifat edukatif dan informatif. Rekomendasi obat bukan pengganti diagnosis dokter. Jika gejala berat, alergi, hamil/menyusui, penyakit kronis, atau keluhan tidak membaik dalam 3 x 24 jam, pengguna perlu diarahkan ke dokter atau apoteker.
