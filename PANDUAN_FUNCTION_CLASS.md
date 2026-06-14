# PANDUAN STRUKTUR PROGRAM: FUNGSI, MODUL, DAN KELAS

Dokumen ini menjelaskan fungsi, modul (controller/model), dan kelas (domain OOP) yang dibangun dalam Sistem Pengolahan Nilai Siswa, lengkap dengan lokasi file, parameter masukan (input), dan hasil keluaran (output).

---

## 1. FUNGSI-FUNGSI UTAMA (Paradigma Terstruktur / Prosedural)
Seluruh fungsi perhitungan matematis dan logika penentu kelulusan dikelompokkan secara terpusat dalam satu file helper.
*   **Path File:** [app/Helpers/structured_helper.php](file:///c:/xampp/htdocs/sistem-nilai/app/Helpers/structured_helper.php)

### A. `validasi_nilai($nilai)`
*   **Tujuan:** Memvalidasi input nilai agar berada di rentang 0 sampai 100 dan bertipe numerik.
*   **Input:** `mixed $nilai` (nilai tugas, UTS, atau UAS).
*   **Output:** `bool` (mengembalikan `true` jika valid, dan `false` jika tidak valid).

### B. `hitung_nilai_akhir($tugas, $uts, $uas)`
*   **Tujuan:** Melakukan penghitungan matematika bobot nilai akhir.
*   **Kalkulasi:** $(Tugas \times 30\%) + (UTS \times 30\%) + (UAS \times 40\%)$.
*   **Input:** `float $tugas`, `float $uts`, `float $uas`.
*   **Output:** `float` (nilai akhir terhitung).

### C. `tentukan_status_kelulusan($nilai_akhir)`
*   **Tujuan:** Menentukan kelayakan kelulusan berdasarkan batas KKM (75).
*   **Input:** `float $nilai_akhir`.
*   **Output:** `string` (mengembalikan `'Lulus'` jika nilai $\ge 75$ atau `'Tidak Lulus'` jika $< 75$).

### D. `proses_laporan($daftar_nilai)`
*   **Tujuan:** Mengolah array berisi riwayat nilai untuk disajikan sebagai ringkasan statistik kelas.
*   **Input:** `array $daftar_nilai` (array data nilai dari database atau objek).
*   **Output:** `array` assosiatif berstruktur:
    ```php
    [
        'tertinggi'            => (float) nilai_tertinggi,
        'terendah'             => (float) nilai_terendah,
        'rata_rata'            => (float) rata_rata_kelas,
        'persentase_kelulusan' => (float) rasio_kelulusan_persen,
        'total_siswa'          => (int) jumlah_siswa,
        'total_lulus'          => (int) jumlah_siswa_lulus,
        'total_tidak_lulus'    => (int) jumlah_siswa_gagal
    ]
    ```

---

## 2. KELAS DOMAIN (Paradigma OOP)
Menyimpan model bisnis murni (domain model) yang memisahkan data dengan database. Menggunakan prinsip enkapsulasi (variabel private + getter/setter).
*   **Path Folder:** `app/OOP/`

### A. Class `Siswa`
*   **Path File:** [app/OOP/Siswa.php](file:///c:/xampp/htdocs/sistem-nilai/app/OOP/Siswa.php)
*   **Atribut:** `private string $nis`, `private string $nama`, `private string $kelas`.
*   **Penggunaan OOP:** Menyimpan data identitas siswa. Memiliki method `toArray()` untuk menyusun properti objek menjadi bentuk array asosiatif sebelum dikirim ke database.

### B. Class `Guru`
*   **Path File:** [app/OOP/Guru.php](file:///c:/xampp/htdocs/sistem-nilai/app/OOP/Guru.php)
*   **Atribut:** `private string $idGuru`, `private string $namaGuru`, `private string $mataPelajaran`.
*   **Penggunaan OOP:** Menyimpan entitas data pengajar.

### C. Class `Nilai`
*   **Path File:** [app/OOP/Nilai.php](file:///c:/xampp/htdocs/sistem-nilai/app/OOP/Nilai.php)
*   **Atribut:** `private ?int $id`, `private Siswa $siswa`, `private Guru $guru`, `private float $nilaiTugas`, `private float $nilaiUts`, `private float $nilaiUas`, `private float $nilaiAkhir`, `private string $statusKelulusan`.
*   **Penggunaan OOP (Agregasi):** Menghubungkan objek `Siswa` dan `Guru` ke dalam objek `Nilai`. Konstruktor memanggil `loadHelper()` dan menjalankan validasi serta kalkulasi otomatis (`kalkulasi()`) saat nilai diset/diubah.

---

## 3. MODUL CONTROLLER & MODEL (Arsitektur MVC)
Mengatur alur data (request HTTP, validasi form, komunikasi model, instansiasi OOP, dan penayangan view).

### A. Autentikasi Pengguna (`AuthController`)
*   **Path File:** [app/Controllers/AuthController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/AuthController.php)
*   **Method:**
    *   `login()`: Menampilkan view form login.
    *   `loginProcess()`: Validasi form input. Jika lolos, mencocokkan password via `password_verify()`, mencari profil foto siswa/guru, dan menyimpan session.
    *   `registerProcess()`: Mendaftar akun baru dengan validasi role dan transaksi database (`$db->transBegin()`).
    *   `logout()`: Menghapus session dan redirect ke halaman login.

### B. CRUD Data Master Siswa & Guru
*   **SiswaController:** [app/Controllers/SiswaController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/SiswaController.php)
*   **GuruController:** [app/Controllers/GuruController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/GuruController.php)
*   **Method:**
    *   `index()`: Menampilkan tabel daftar data master.
    *   `create()` & `store()`: Menampilkan form tambah dan menyimpannya setelah divalidasi.
    *   `edit($id)` & `update($id)`: Menampilkan form edit dan memproses update data.
    *   `delete($id)`: Menghapus data berdasarkan primary key.
    *   `profil()` & `profilUpdate()`: Memungkinkan Siswa/Guru mengelola data diri & mengunggah foto profil sendiri.

### C. Pengolahan Nilai (`NilaiController`)
*   **Path File:** [app/Controllers/NilaiController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/NilaiController.php)
*   **Method Utama:**
    *   `store()` & `update()`: Mengambil data siswa dan guru dari model database, meng-instansiasi objek `Siswa` dan `Guru` OOP, lalu memasukannya ke objek `Nilai` untuk mengkalkulasi Nilai Akhir sebelum memanggil model database untuk menyimpan datanya.

### D. Rekapitulasi Laporan (`LaporanController`)
*   **Path File:** [app/Controllers/LaporanController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/LaporanController.php)
*   **Method Utama:**
    *   `index()`: Melakukan kalkulasi rata-rata per siswa untuk tabel rangkuman.
    *   `rapor($nis)`: Menampilkan halaman cetak rapor per siswa dengan memproses statistik kelulusan mapel menggunakan helper terstruktur.
