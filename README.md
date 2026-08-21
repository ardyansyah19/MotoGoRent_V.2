# MotoGoRent — Sistem Login Customer & Sewa Motor

Sistem PHP native (tanpa framework) + MySQL untuk registrasi/login customer,
katalog motor dengan sisa unit real-time, dan live chat customer ↔ admin.

## Struktur Folder

```
motogorent-app/
├── admin/              -> login, dashboard, chat admin
├── auth/                -> register, login, logout customer
├── config/
│   └── database.php     -> konfigurasi koneksi database (EDIT INI)
├── css/
├── customer/            -> beranda & chat customer
├── includes/            -> auth guard & fungsi bantuan
├── js/
│   └── chat.js
├── sql/
│   └── motogorent.sql   -> import ke database
└── index.php
```

## Cara Instalasi (XAMPP)

1. Copy folder `motogorent-app` ke dalam `htdocs` (XAMPP) atau document root server kamu.
2. Buka **phpMyAdmin**, buat database baru bernama `motogorent` (atau import langsung
   `sql/motogorent.sql`, yang otomatis membuat database + tabel + data awal).
3. Buka `config/database.php`, sesuaikan `DB_HOST`, `DB_USER`, `DB_PASS` jika perlu
   (default XAMPP: user `root`, password kosong).
4. Jalankan Apache & MySQL dari XAMPP Control Panel.
5. Akses di browser: `http://localhost/motogorent-app/`

## Akun Default

**Admin** (untuk membalas chat & kelola stok)
- URL: `http://localhost/motogorent-app/admin/login.php`
- Email: `admin@motogorent.com`
- Password: `admin123`

**Customer**
- Daftar akun baru sendiri lewat `http://localhost/motogorent-app/auth/register.php`

## Alur Penggunaan

1. Customer daftar (nama lengkap, email, password) di halaman registrasi.
2. Customer login hanya dengan email & password.
3. Di halaman beranda, customer melihat daftar motor lengkap dengan **sisa unit
   ready**. Motor yang stoknya 0 otomatis ditandai "Habis" dan tombolnya nonaktif.
4. Tombol **Hubungi Admin** pada tiap motor membuka halaman chat dan otomatis
   mengisi pesan pembuka sesuai motor yang dipilih.
5. Chat berjalan dua arah secara real-time (polling tiap 3 detik) — admin membalas
   dari Dashboard Admin.
6. Admin bisa mengubah **sisa unit** tiap motor langsung dari Dashboard Admin.

## Catatan Keamanan

- Password disimpan ter-hash dengan `password_hash()` (bcrypt), diverifikasi
  dengan `password_verify()`.
- Semua query database memakai **prepared statement** (PDO) — aman dari SQL Injection.
- Form registrasi & login dilindungi **CSRF token**.
- Semua output di-escape dengan `htmlspecialchars()` — aman dari XSS.

## Yang Perlu Disesuaikan

- Ganti data motor & harga di tabel `motors` sesuai armada asli kamu.
- Ganti password admin default setelah instalasi (lewat phpMyAdmin, gunakan
  `password_hash()` PHP untuk membuat hash baru).
- Jika deploy ke hosting (bukan `localhost/motogorent-app/`), sesuaikan semua
  path `redirect('/motogorent-app/...')` di file-file `auth_customer.php`,
  `auth_admin.php`, `login.php`, `logout.php`, dll., agar cocok dengan folder
  instalasi kamu di server.
