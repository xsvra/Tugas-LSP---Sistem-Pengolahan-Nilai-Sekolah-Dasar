# Setup Environment Project

## Spesifikasi Minimum Environment

Pastikan perangkat yang digunakan memenuhi spesifikasi berikut:

- Git 2.45+
- PHP 8.2+
- Composer 2.8+
- Node.js 22 LTS+
- NPM 10+
- MySQL 8.0+
- CodeIgniter 4.6+
- Tailwind CSS 4.x
- Vite 7.x

---

# 1. Install Git

## Download Git

Unduh Git melalui website resmi:

https://git-scm.com/downloads

Pilih installer sesuai sistem operasi yang digunakan.

## Install Git

1. Jalankan installer Git.
2. Klik **Next** hingga selesai.
3. Gunakan konfigurasi default.
4. Klik **Install**.
5. Tunggu hingga proses instalasi selesai.

## Verifikasi Instalasi Git

Buka Command Prompt atau Terminal lalu jalankan:

```bash
git --version
```

Contoh output:

```text
git version 2.50.1.windows.1
```

## Konfigurasi Git (Opsional)

Atur username:

```bash
git config --global user.name "Nama Anda"
```

Atur email:

```bash
git config --global user.email "email@example.com"
```

Cek konfigurasi:

```bash
git config --list
```

---

# 2. Clone Repository

Clone repository dari GitHub:

```bash
git clone https://github.com/username/nama-repository.git
```

Masuk ke folder project:

```bash
cd nama-repository
```

Cek status repository:

```bash
git status
```

---

# 3. Install PHP

## Download PHP

Unduh PHP melalui:

https://windows.php.net/download/

Disarankan menggunakan PHP 8.2 atau versi terbaru yang kompatibel dengan project.

## Ekstrak PHP

Ekstrak hasil download ke lokasi berikut:

```text
C:\php
```

Contoh struktur folder:

```text
C:\php
├── php.exe
├── php.ini
└── ext
```

## Menambahkan PHP ke Environment Variable

1. Tekan `Windows + R`
2. Ketik:

```text
sysdm.cpl
```

3. Pilih tab **Advanced**
4. Klik **Environment Variables**
5. Pada bagian **System Variables**, pilih **Path**
6. Klik **Edit**
7. Klik **New**
8. Tambahkan:

```text
C:\php
```

9. Klik **OK** hingga seluruh jendela tertutup.

## Verifikasi Instalasi PHP

Buka Command Prompt baru lalu jalankan:

```bash
php -v
```

Contoh output:

```text
PHP 8.2.x (cli)
```

Cek lokasi PHP:

```bash
where php
```

---

# 4. Install Composer

## Download Composer

Unduh Composer melalui:

https://getcomposer.org/download/

## Install Composer

1. Jalankan installer Composer.
2. Saat diminta lokasi PHP, arahkan ke:

```text
C:\php\php.exe
```

3. Selesaikan proses instalasi.

## Verifikasi Composer

```bash
composer --version
```

Contoh output:

```text
Composer version 2.x.x
```

---

# 5. Install CodeIgniter 4

## Verifikasi Framework CodeIgniter

Setelah project berhasil di-clone dan dependency diinstall, cek versi CodeIgniter:

```bash
composer show codeigniter4/framework
```

Atau:

```bash
php spark --version
```

Contoh output:

```text
CodeIgniter v4.x.x
```

---

# 6. Install Node.js dan NPM

## Download Node.js

Unduh Node.js melalui:

https://nodejs.org

Gunakan versi **LTS (Long Term Support)**.

## Install Node.js

1. Jalankan installer.
2. Klik **Next** hingga selesai.
3. Pastikan opsi **Add to PATH** aktif.
4. Klik **Install**.

## Verifikasi Node.js

```bash
node -v
```

Contoh output:

```text
v22.x.x
```

## Verifikasi NPM

```bash
npm -v
```

Contoh output:

```text
10.x.x
```

---

# 7. Install Dependency Project

## Install Dependency Backend

Jalankan:

```bash
composer install
```

Perintah ini akan menginstall seluruh dependency yang tercantum pada file:

```text
composer.json
```

## Install Dependency Frontend

Jalankan:

```bash
npm install
```

Perintah ini akan menginstall seluruh dependency yang tercantum pada file:

```text
package.json
```

---

# 8. Verifikasi Tailwind CSS

Project ini menggunakan Tailwind CSS.

Untuk memastikan Tailwind CSS telah terinstall:

```bash
npm list tailwindcss
```

Contoh output:

```text
tailwindcss@4.x.x
```

---

# 9. Setup Environment Project

Salin file environment:

### Windows

```cmd
copy env .env
```

### Linux / MacOS

```bash
cp env .env
```

Sesuaikan konfigurasi database pada file `.env`:

```env
database.default.hostname = localhost
database.default.database = nama_database
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

---

# 10. Setup Database

1. Buat database baru di MySQL.
2. Sesuaikan nama database pada file `.env`.
3. Jalankan migration apabila project menggunakan migration:

```bash
php spark migrate
```

4. Jalankan seeder apabila tersedia:

```bash
php spark db:seed NamaSeeder
```

---

# 11. Menjalankan Project

## Menjalankan Backend CodeIgniter 4

```bash
php spark serve
```

Output:

```text
CodeIgniter development server started on http://localhost:8080
```

Akses:

```text
http://localhost:8080
```

## Menjalankan Frontend (Vite + Tailwind CSS)

Buka terminal baru kemudian jalankan:

```bash
npm run dev
```

Contoh output:

```text
VITE ready in xxx ms

➜ Local: http://localhost:5173/
```

Akses:

```text
http://localhost:5173
```

---

# 12. Build Production

Untuk membuat build production:

```bash
npm run build
```

Hasil build akan tersimpan pada folder build yang telah dikonfigurasi pada project.

---

# 13. Verifikasi Keseluruhan Setup

Pastikan seluruh perintah berikut berhasil dijalankan:

```bash
git --version
php -v
composer --version
node -v
npm -v
```

---

# 14. Troubleshooting

## PHP Tidak Terdeteksi

Jika muncul:

```text
'php' is not recognized as an internal or external command
```

Pastikan folder PHP sudah ditambahkan ke Environment Variable PATH.

---

## Composer Tidak Terdeteksi

Jika muncul:

```text
'composer' is not recognized as an internal or external command
```

Lakukan instalasi ulang Composer dan pastikan Composer telah masuk ke PATH.

---

## Node.js Tidak Terdeteksi

Jika muncul:

```text
'node' is not recognized as an internal or external command
```

Install ulang Node.js dan pastikan opsi **Add to PATH** dicentang saat instalasi.

---

## Membersihkan Dependency

Jika terjadi error dependency:

### Hapus folder vendor

```bash
composer clear-cache
```

Kemudian install ulang:

```bash
composer install
```

### Hapus folder node_modules

```bash
rm -rf node_modules
```

Install ulang:

```bash
npm install
```

---

## Clear Cache CodeIgniter

```bash
php spark cache:clear
```

---

## Regenerate Autoload Composer

```bash
composer dump-autoload
```

---

## Cek Dependency yang Terinstall

### Composer

```bash
composer show
```

### NPM

```bash
npm list
```

---

# Setup Berhasil

Jika seluruh langkah di atas telah berhasil dilakukan, maka project siap dijalankan pada perangkat baru.