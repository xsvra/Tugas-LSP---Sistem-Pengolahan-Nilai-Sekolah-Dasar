# PANDUAN SETUP ENVIRONMENT (WINDOWS & CLEAN DEVICE)

Panduan ini ditujukan untuk menjalankan project **Sistem Pengolahan Nilai Siswa** pada perangkat baru (clean device) menggunakan **XAMPP** (bundel Apache, PHP, dan MySQL), **Composer**, dan **Node.js** untuk kompilasi Tailwind CSS.

---

## 1. Persiapan Alat (Prerequisites)
Unduh dan install software berikut terlebih dahulu:
1.  **XAMPP (PHP 8.2+ & MySQL):** [Unduh XAMPP](https://www.apachefriends.org/download.html)
    *   *Catatan:* Pilih installer dengan versi PHP 8.2 atau lebih tinggi.
2.  **Git (Version Control):** [Unduh Git](https://git-scm.com/downloads)
3.  **Composer (Dependency Manager PHP):** [Unduh Composer](https://getcomposer.org/download/)
4.  **Node.js (LTS Version - untuk compiler Tailwind):** [Unduh Node.js](https://nodejs.org/)

---

## 2. Langkah-Langkah Instalasi & Setup

### Langkah A: Clone atau Pindahkan Project
1.  Buka terminal (Command Prompt / Git Bash).
2.  Masuk ke direktori root web server XAMPP (`htdocs`):
    ```bash
    cd C:\xampp\htdocs
    ```
3.  Clone repository dari GitHub ke folder `sistem-nilai`:
    ```bash
    git clone https://github.com/xsvra/Tugas-LSP---Sistem-Pengolahan-Nilai-Sekolah-Dasar.git sistem-nilai
    ```
4.  Masuk ke folder project:
    ```bash
    cd sistem-nilai
    ```

### Langkah B: Instalasi Dependency Backend (Composer)
1.  Di dalam folder project `C:\xampp\htdocs\sistem-nilai`, jalankan perintah:
    ```bash
    composer install
    ```
2.  Perintah ini mengunduh framework CodeIgniter 4 beserta dependensi lainnya ke dalam folder `vendor`.

### Langkah C: Instalasi Dependency Frontend (Tailwind CSS)
Project ini menggunakan Tailwind CSS versi 3.4 CLI untuk menyusun file CSS.
1.  Jalankan perintah berikut untuk mengunduh compiler CSS:
    ```bash
    npm install
    ```
2.  *Optional:* Untuk menyusun ulang file CSS selama proses pengembangan, jalankan perintah:
    ```bash
    npm run watch
    ```

### Langkah D: Konfigurasi Environment File (`.env`)
1.  Salin file `env` bawaan menjadi `.env`:
    *   **Windows (Command Prompt):**
        ```cmd
        copy env .env
        ```
    *   **Git Bash / Powershell:**
        ```bash
        cp env .env
        ```
2.  Buka file `.env` menggunakan editor teks (VS Code, Notepad, dll).
3.  Ubah bagian konfigurasi database agar sesuai dengan environment lokal Anda:
    ```env
    database.default.hostname = localhost
    database.default.database = sistem_nilai
    database.default.username = root
    database.default.password =
    database.default.DBDriver = MySQLi
    ```

### Langkah E: Inisialisasi Database (Reset & Seed)
Aplikasi ini menyediakan script khusus untuk membuat skema tabel dan mengisi data awal (*seed data*) secara otomatis.
1.  Aktifkan control panel **XAMPP** dan start modul **Apache** & **MySQL**.
2.  Jalankan script reset database menggunakan PHP CLI:
    ```bash
    php reset_db.php
    ```
3.  Script tersebut otomatis akan:
    *   Membuat database `sistem_nilai` (jika belum ada).
    *   Mengimpor struktur tabel dari `database.sql`.
    *   Memasukkan data dummy awal untuk Admin, Guru, Siswa, dan Nilai.

---

## 3. Menjalankan Aplikasi

1.  Jalankan development server bawaan CodeIgniter:
    ```bash
    php spark serve
    ```
2.  Buka web browser dan akses aplikasi melalui URL:
    ```text
    http://localhost:8080
    ```
3.  **Akun Uji Coba Bawaan:**
    *   **Admin:** Username: `admin` | Password: `admin123`
    *   **Guru:** Username: `budi` | Password: `sekolah123`
    *   **Siswa:** Username: `ani` | Password: `sekolah123`

---

## 4. Troubleshooting (Mengatasi Kendala)

*   **Error: `PHP is not recognized...`**
    *   *Solusi:* Cari folder php instalasi XAMPP (biasanya `C:\xampp\php`), lalu daftarkan path tersebut ke dalam Environment Variables Windows pada variabel `Path`.
*   **Error: `Extension 'intl' is missing...`**
    *   *Solusi:* Buka file `php.ini` di folder `C:\xampp\php\php.ini`, temukan baris `;extension=intl`, hapus tanda titik koma (`;`) di depannya menjadi `extension=intl`, simpan, lalu restart XAMPP Apache.