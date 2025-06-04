# ✨ Task Flow - Aplikasi Manajemen Tugas Sederhana ✨

Selamat datang di Task Flow! Aplikasi web ini dirancang untuk membantu pengguna (individu maupun tim) dalam mengelola tugas sehari-hari dengan lebih terstruktur dan efisien. 🚀

## 📝 Deskripsi Proyek

Task Flow adalah aplikasi berbasis PHP dan MySQL yang memungkinkan pengguna untuk:
* Mendaftar dan Login dengan aman (password di-hash) 🔐
* Membuat, melihat, mengedit, dan menghapus daftar tugas pribadi. 🗒️
* Mengatur status tugas (Berjalan, Selesai, Tertunda) dengan visualisasi yang jelas. 📊
* Mengelola profil pengguna, termasuk foto profil. 👤
* Memiliki panel admin untuk pengelolaan lebih lanjut (jika diimplementasikan sepenuhnya). 👑

Proyek ini dibangun dengan antarmuka pengguna yang bersih dan responsif menggunakan Bootstrap 5.

## 🛠️ Fitur Utama

* **Autentikasi Pengguna:**
    * Registrasi pengguna baru.
    * Login pengguna dengan verifikasi password yang di-hash.
    * Sistem Lupa Password (jika `lupapass.php` diimplementasikan sepenuhnya).
    * Logout.
* **Manajemen Tugas (CRUD):**
    * **Create:** Menambahkan tugas baru melalui modal.
    * **Read:** Menampilkan daftar tugas dengan detail (judul, deskripsi, tanggal, status).
    * **Update:** Mengedit tugas yang sudah ada melalui modal.
    * **Delete:** Menghapus tugas.
* **Dashboard Pengguna:**
    * Menampilkan ringkasan jumlah tugas berdasarkan status (Berjalan, Selesai, Tertunda).
* **Manajemen Profil:**
    * Melihat dan mengedit informasi profil (nama, email, kontak, peran/level, foto).
* **Panel Admin (Dasar):**
    * Dashboard admin terpisah.
    * Navigasi khusus admin di berbagai halaman jika login sebagai admin.
    * Admin dapat melihat semua tugas pengguna (jika diaktifkan).

## 🔧 Teknologi yang Digunakan

* **Backend:** PHP
* **Database:** MySQL (koneksi via `mysqli`)
* **Frontend:** HTML, CSS, JavaScript
* **Framework/Library:**
    * Bootstrap 5 (untuk UI dan komponen responsif)
    * Font Awesome (untuk ikon)

## ⚙️ Struktur Database

Database `managemen_tugas` terdiri dari tabel utama berikut:
1.  `user`: Menyimpan data pengguna (termasuk `level` untuk admin).
2.  `tugas`: Menyimpan detail tugas yang berelasi dengan `id_user`.
3.  `admin`: (Struktur ada, namun fungsionalitas login terpisah untuk tabel ini belum diimplementasikan di kode PHP saat ini, admin menggunakan `level` di tabel `user`).

*(Anda bisa menambahkan detail lebih lanjut mengenai kolom-kolom penting di sini jika diinginkan)*

## 🚀 Cara Menjalankan Proyek

1.  **Clone Repositori (jika ada):**
    ```bash
    git clone [https://www.andarepository.com/](https://www.andarepository.com/)
    cd [nama direktori proyek]
    ```
2.  **Setup Database:**
    * Buat database dengan nama `managemen_tugas` di MySQL/phpMyAdmin.
    * Impor struktur tabel. Anda bisa menggunakan kueri SQL yang telah dibuat sebelumnya (berdasarkan gambar struktur tabel) atau membuat file `database.sql` untuk diimpor.
3.  **Konfigurasi Koneksi Database:**
    * Pastikan detail koneksi di `db.php` (host, user, password, nama database) sudah sesuai dengan pengaturan server lokal Anda.
    ```php
    <?php
    $host = "localhost";
    $user = "root"; // Sesuaikan jika berbeda
    $pass = "";     // Sesuaikan jika berbeda
    $db = "managemen_tugas";
    // ...
    ?>
    ```
4.  **Web Server:**
    * Tempatkan direktori proyek di dalam direktori `htdocs` (untuk XAMPP/MAMP) atau `www` (untuk WAMP) pada server lokal Anda.
    * Jalankan Apache dan MySQL dari panel kontrol server lokal Anda.
5.  **Akses Aplikasi:**
    * Buka browser dan akses aplikasi melalui URL seperti `http://localhost/[nama_direktori_proyek]/`.
6.  **Membuat Akun Admin:**
    * Daftar sebagai pengguna baru melalui halaman registrasi.
    * Ubah `level` pengguna tersebut menjadi `admin` secara manual di tabel `user` melalui phpMyAdmin.
    * Login menggunakan akun admin tersebut untuk mengakses `admin_dashboard.php`.

## 🤝 Kontribusi

Saat ini proyek ini dikelola secara internal. Namun, masukan dan saran selalu diterima!

---

Terima kasih telah menggunakan Task Flow! 🎉
