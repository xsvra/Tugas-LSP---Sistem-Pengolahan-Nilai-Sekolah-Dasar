<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="doc-sheet">
    <div style="border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">
        <h2 style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">Rancangan Sistem Pengolahan Nilai Siswa</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Dokumen Spesifikasi Teknis Integrasi Paradigma OOP dan Terstruktur (Tugas 1)</p>
    </div>

    <!-- Section 1: Konsep & Arsitektur -->
    <div class="doc-section">
        <h2>1. Arsitektur MVC (Model-View-Controller)</h2>
        <p>Aplikasi ini dirancang menggunakan arsitektur MVC bawaan dari framework <strong>CodeIgniter 4</strong> untuk memisahkan logika bisnis, presentasi, dan akses data secara modular:</p>
        
        <ul style="margin-left: 1.5rem; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem; color: var(--text-muted);">
            <li><strong>Model:</strong> Berfungsi mengelola interaksi dengan database MySQL (XAMPP). Diimplementasikan dalam <a href="<?= base_url('siswa') ?>">SiswaModel</a>, <a href="<?= base_url('guru') ?>">GuruModel</a>, dan <a href="<?= base_url('nilai') ?>">NilaiModel</a> yang mendefinisikan validasi database serta relasi database.</li>
            <li><strong>View:</strong> Berfungsi menampilkan antarmuka web (UI) menggunakan template engine CodeIgniter. Tampilan menggunakan desain premium modern dengan glassmorphism dan tata letak responsif.</li>
            <li><strong>Controller:</strong> Berfungsi sebagai jembatan yang memproses input pengguna, memanggil model data, menginstansiasi objek domain OOP, memproses helper terstruktur, dan merender tampilan yang sesuai.</li>
        </ul>
    </div>

    <!-- Section 2: Integrasi Dua Paradigma -->
    <div class="doc-section">
        <h2>2. Integrasi Dua Paradigma Pemrograman</h2>
        <p>Sistem ini menggabungkan paradigma <strong>Pemrograman Terstruktur (Procedural)</strong> dan <strong>Pemrograman Berorientasi Objek (OOP)</strong> untuk mencapai keterbacaan kode (readability) dan reusable logic yang optimal:</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
            <!-- Paradigma Terstruktur -->
            <div style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 1.25rem;">
                <h4 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-code"></i> Pemrograman Terstruktur
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    Diimplementasikan melalui berkas helper independen di <code>app/Helpers/structured_helper.php</code>.
                </p>
                <ul style="margin-left: 1.25rem; font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.25rem;">
                    <li><code>validasi_nilai($nilai)</code>: Memvalidasi rentang input (0-100).</li>
                    <li><code>hitung_nilai_akhir($tugas, $uts, $uas)</code>: Melakukan kalkulasi bobot (30%, 30%, 40%).</li>
                    <li><code>tentukan_status_kelulusan($nilai)</code>: Membandingkan nilai akhir dengan batas KKM (75).</li>
                    <li><code>proses_laporan($daftar)</code>: Menghasilkan ringkasan statistik kelas (tertinggi, terendah, rata-rata, kelulusan).</li>
                </ul>
            </div>

            <!-- Paradigma OOP -->
            <div style="background-color: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 1.25rem;">
                <h4 style="font-family: var(--font-heading); color: var(--success); font-size: 1.1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-cube"></i> Pemrograman Berorientasi Objek
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    Diimplementasikan menggunakan kelas-kelas domain murni (Domain Entities) di direktori <code>app/OOP/</code>.
                </p>
                <ul style="margin-left: 1.25rem; font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.25rem;">
                    <li><code>Siswa</code>: Merepresentasikan entitas siswa dengan atribut private <code>$nis</code>, <code>$nama</code>, <code>$kelas</code>, beserta getter/setter.</li>
                    <li><code>Guru</code>: Merepresentasikan entitas guru dengan atribut private <code>$idGuru</code>, <code>$namaGuru</code>, <code>$mataPelajaran</code>.</li>
                    <li><code>Nilai</code>: Menggabungkan objek <code>Siswa</code> dan <code>Guru</code> untuk mengintegrasikan nilai akademik. Kelas ini memanggil helper terstruktur secara otomatis saat objek dibuat atau diubah untuk melakukan perhitungan nilai akhir dan kelulusan.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Section 3: Skema Database -->
    <div class="doc-section">
        <h2>3. Skema Relasional Database (ERD)</h2>
        <p>Database dinamakan <code>sistem_nilai</code>. Terdiri dari 3 tabel utama yang saling berelasi secara erat dengan aturan integritas referensial (Foreign Key) yang disetel ke <code>ON DELETE CASCADE</code>:</p>

        <div class="schema-block">
            <!-- Table Siswa -->
            <div class="schema-table">
                <div class="schema-title">Tabel: siswa</div>
                <ul class="schema-fields">
                    <li><span class="field-name">nis (PK)</span> <span class="field-type">VARCHAR(20)</span></li>
                    <li><span class="field-name">nama</span> <span class="field-type">VARCHAR(100)</span></li>
                    <li><span class="field-name">kelas</span> <span class="field-type">VARCHAR(50)</span></li>
                    <li><span class="field-name">created_at</span> <span class="field-type">TIMESTAMP</span></li>
                    <li><span class="field-name">updated_at</span> <span class="field-type">TIMESTAMP</span></li>
                </ul>
            </div>

            <!-- Table Guru -->
            <div class="schema-table">
                <div class="schema-title">Tabel: guru</div>
                <ul class="schema-fields">
                    <li><span class="field-name">id_guru (PK)</span> <span class="field-type">VARCHAR(20)</span></li>
                    <li><span class="field-name">nama_guru</span> <span class="field-type">VARCHAR(100)</span></li>
                    <li><span class="field-name">mata_pelajaran</span> <span class="field-type">VARCHAR(100)</span></li>
                    <li><span class="field-name">created_at</span> <span class="field-type">TIMESTAMP</span></li>
                    <li><span class="field-name">updated_at</span> <span class="field-type">TIMESTAMP</span></li>
                </ul>
            </div>

            <!-- Table Nilai -->
            <div class="schema-table">
                <div class="schema-title">Tabel: nilai (Transaksi)</div>
                <ul class="schema-fields">
                    <li><span class="field-name">id (PK)</span> <span class="field-type">INT AUTO_INCREMENT</span></li>
                    <li><span class="field-name">nis (FK)</span> <span class="field-type">VARCHAR(20)</span></li>
                    <li><span class="field-name">id_guru (FK)</span> <span class="field-type">VARCHAR(20)</span></li>
                    <li><span class="field-name">nilai_tugas</span> <span class="field-type">DECIMAL(5,2)</span></li>
                    <li><span class="field-name">nilai_uts</span> <span class="field-type">DECIMAL(5,2)</span></li>
                    <li><span class="field-name">nilai_uas</span> <span class="field-type">DECIMAL(5,2)</span></li>
                    <li><span class="field-name">nilai_akhir</span> <span class="field-type">DECIMAL(5,2)</span></li>
                    <li><span class="field-name">status_kelulusan</span> <span class="field-type">VARCHAR(20)</span></li>
                </ul>
            </div>
        </div>

        <div class="doc-diagram">
+------------------+             +-------------------+             +------------------+
|      siswa       |             |       nilai       |             |       guru       |
+------------------+             +-------------------+             +------------------+
| PK | nis         | <-----+     | PK | id           |     +-----> | PK | id_guru     |
|    | nama        |       |     | FK | nis         |     |       |    | nama_guru   |
|    | kelas       |       +---- | FK | id_guru     | ----+       |    | mapel       |
|    | created_at  |             |    | nilai_tugas  |             |    | created_at  |
|    | updated_at  |             |    | nilai_uts    |             |    | updated_at  |
+------------------+             |    | nilai_uas    |             +------------------+
                                 |    | nilai_akhir  |
                                 |    | status_lulus |
                                 |    | created_at   |
                                 |    | updated_at   |
                                 +-------------------+
        </div>
    </div>

    <!-- Section 4: Alur Input Nilai (Integrasi OOP & Terstruktur) -->
    <div class="doc-section">
        <h2>4. Alur Bisnis Input Nilai Siswa</h2>
        <p>Berikut adalah visualisasi alur bagaimana data nilai diolah di dalam sistem menggunakan gabungan OOP dan Helper Terstruktur:</p>
        <div class="doc-diagram">
[Form Input Nilai] (User mengirim Tugas, UTS, UAS, NIS, ID Guru)
       │
       ▼
[NilaiController::store] (Mengambil data siswa dan guru dari DB)
       │
       ▼
[Instansiasi Objek OOP] 
  1. $siswaObj = new Siswa($nis, $nama, $kelas);
  2. $guruObj = new Guru($id_guru, $nama_guru, $mapel);
  3. $nilaiObj = new Nilai(null, $siswaObj, $guruObj, $tugas, $uts, $uas);
       │
       ▼
[Di dalam Constructor Class Nilai] ──► Memanggil helper terstruktur:
                                       1. validasi_nilai()
                                       2. hitung_nilai_akhir()
                                       3. tentukan_status_kelulusan()
       │
       ▼
[NilaiController::store] ──► Memanggil $nilaiObj->toArray() untuk mendapatkan data array.
       │
       ▼
[NilaiModel::save] (Data disimpan dengan aman ke MySQL)
        </div>
    </div>
</div>

<?= $this->endSection() ?>
