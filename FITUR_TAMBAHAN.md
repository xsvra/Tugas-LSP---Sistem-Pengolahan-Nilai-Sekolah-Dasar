# PANDUAN PENGEMBANGAN 10 FITUR TAMBAHAN (UJIKOM)

Dokumen ini berisi panduan teknis mendetail tentang cara membuat 10 fitur tambahan atau modifikasi logika pada Sistem Pengolahan Nilai Siswa. Setiap fitur dilengkapi dengan lokasi file, petunjuk langkah demi langkah, dan potongan kode siap pakai (*copy-paste*) untuk mempermudah pengerjaan saat ujian.

---

## 1. MENU KELOLA NILAI OLEH ADMIN (AKSES & FILTER MAKSIMAL)
*   **Tujuan:** Menambahkan menu "Kelola Nilai" di role Admin dengan tampilan dan perilaku yang mirip dengan Guru, namun filter Mapel dan Kelasnya mencakup keseluruhan data di sekolah (tidak dibatasi seperti Guru).

### Langkah-langkah Pembuatan Secara Detail:

#### **Langkah 1: Tambahkan Link Menu pada Sidebar Admin**
*   **Nama File:** [app/Views/layout.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/layout.php)
*   **Deskripsi:** Menampilkan menu "Kelola Nilai" di panel sidebar milik Admin.
*   **Kode Modifikasi (Cari baris 35-41, sebelum `<?php endif; ?>` milik admin):**
    ```html
    <!-- Tambahkan menu ini di bawah menu Data Guru milik admin -->
    <li>
        <a href="<?= base_url('/nilai') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/nilai')) !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice"></i>
            <span>Kelola Nilai</span>
        </a>
    </li>
    ```

#### **Langkah 2: Izinkan Admin Mengakses Route Nilai**
*   **Nama File:** [app/Config/Routes.php](file:///c:/xampp/htdocs/sistem-nilai/app/Config/Routes.php)
*   **Deskripsi:** Memindahkan grup route `/nilai` agar dikawal oleh filter wewenang `auth:admin,guru`.
*   **Kode Modifikasi:**
    ```php
    // Ganti grup filter wewenang guru (Baris 44) agar juga bisa diakses oleh admin:
    $routes->group('', ['filter' => 'auth:admin,guru'], function($routes) {
        // Pindahkan/pastikan group route 'nilai' berada di sini:
        $routes->group('nilai', function($routes) {
            $routes->get('/', 'NilaiController::index');
            $routes->get('create', 'NilaiController::create');
            $routes->post('store', 'NilaiController::store');
            $routes->get('edit/(:num)', 'NilaiController::edit/$1');
            $routes->post('update/(:num)', 'NilaiController::update/$1');
            $routes->get('delete/(:num)', 'NilaiController::delete/$1');
            $routes->get('siswa-by-kelas/(:any)', 'NilaiController::getSiswaByKelas/$1');
        });
    });
    ```

#### **Langkah 3: Sesuaikan Method Index di NilaiController**
*   **Nama File:** [app/Controllers/NilaiController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/NilaiController.php)
*   **Deskripsi:** Mengambil semua data nilai sekolah dan memuat data seluruh kelas serta mapel yang terdaftar jika user yang login adalah Admin.
*   **Kode Modifikasi pada `index()`:**
    ```php
    public function index()
    {
        $nilaiModel = new NilaiModel();
        $role = session()->get('role');
        $idGuru = session()->get('ref_id');
        $kelasDiajar = [];
        $mapelDiajar = [];

        if ($role === 'guru') {
            $guruModel = new GuruModel();
            $guru = $guruModel->find($idGuru);
            $kelasDiajar = !empty($guru['kelas_diajar']) ? explode(',', $guru['kelas_diajar']) : [];
            $mapelDiajar = !empty($guru['mata_pelajaran']) ? explode(',', $guru['mata_pelajaran']) : [];

            $nilai = $nilaiModel->select('nilai.*, siswa.nama as nama_siswa, siswa.kelas, guru.nama_guru')
                ->join('siswa', 'siswa.nis = nilai.nis')
                ->join('guru', 'guru.id_guru = nilai.id_guru')
                ->where('nilai.id_guru', $idGuru)
                ->orderBy('nilai.created_at', 'DESC')
                ->findAll();
        } else {
            // Admin melihat semua nilai
            $nilai = $nilaiModel->getNilaiWithDetails();
            
            // Ambil semua data mapel & kelas unik dari tabel pemetaan guru
            $db = \Config\Database::connect();
            $mapelDiajar = array_column($db->table('guru_mapel_kelas')->select('mata_pelajaran')->distinct()->get()->getResultArray(), 'mata_pelajaran');
            $kelasDiajar = array_column($db->table('guru_mapel_kelas')->select('kelas')->distinct()->orderBy('kelas', 'ASC')->get()->getResultArray(), 'kelas');
        }

        $data = [
            'title'       => 'Daftar Nilai Siswa',
            'nilai'       => $nilai,
            'kelasDiajar' => $kelasDiajar,
            'mapelDiajar' => $mapelDiajar
        ];
        return view('nilai/index', $data);
    }
    ```

#### **Langkah 4: Tampilkan Dropdown Filter untuk Admin**
*   **Nama File:** [app/Views/nilai/index.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/nilai/index.php)
*   **Deskripsi:** Membuka baris filter mapel dan kelas di halaman depan agar bisa dilihat dan digunakan oleh Admin.
*   **Kode Modifikasi (Ubah Baris 14):**
    ```html
    <!-- Ganti baris 14 dari:
    <?php if (session()->get('role') === 'guru' && (!empty($kelasDiajar) || !empty($mapelDiajar))): ?>
    Menjadi: -->
    <?php if ((session()->get('role') === 'guru' || session()->get('role') === 'admin') && (!empty($kelasDiajar) || !empty($mapelDiajar))): ?>
    ```

#### **Langkah 5: Sesuaikan Method Create di NilaiController**
*   **Nama File:** [app/Controllers/NilaiController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/NilaiController.php)
*   **Deskripsi:** Menyiapkan data dropdown filter untuk Admin agar memuat keseluruhan data mapping guru-kelas-mapel saat input nilai baru.
*   **Kode Modifikasi pada `create()`:**
    ```php
    public function create()
    {
        $role = session()->get('role');
        $idGuru = session()->get('ref_id');
        $mapelDiajar = [];
        $kelasDiajar = [];
        $mappings = [];

        if ($role === 'guru') {
            $guruModel = new GuruModel();
            $guru = $guruModel->find($idGuru);
            $mapelDiajar = !empty($guru['mata_pelajaran']) ? explode(',', $guru['mata_pelajaran']) : [];
            $kelasDiajar = !empty($guru['kelas_diajar']) ? explode(',', $guru['kelas_diajar']) : [];

            $db = \Config\Database::connect();
            $mappings = $db->table('guru_mapel_kelas')
                ->where('id_guru', $idGuru)
                ->get()
                ->getResultArray();
        } else if ($role === 'admin') {
            // Admin memuat seluruh mapping guru agar bisa memilih semua mapel & kelas
            $db = \Config\Database::connect();
            $mappings = $db->table('guru_mapel_kelas')->get()->getResultArray();
            $mapelDiajar = array_values(array_unique(array_column($mappings, 'mata_pelajaran')));
            $kelasDiajar = array_values(array_unique(array_column($mappings, 'kelas')));
            sort($kelasDiajar);
        }

        $data = [
            'title'        => 'Input Nilai Siswa',
            'mapelDiajar'  => $mapelDiajar,
            'kelasDiajar'  => $kelasDiajar,
            'mappings'     => $mappings,
            'validation'   => \Config\Services::validation()
        ];
        return view('nilai/create', $data);
    }
    ```

#### **Langkah 6: Sesuaikan Method Store & Update untuk Menemukan Guru Pengampu**
*   **Nama File:** [app/Controllers/NilaiController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/NilaiController.php)
*   **Deskripsi:** Admin tidak memiliki `ref_id` (ID Guru), sehingga sistem harus mendeteksi secara otomatis ID Guru pengampu mapel tersebut di kelas yang dipilih agar data tersimpan dengan benar.
*   **Kode Modifikasi pada `store()`:**
    ```php
    // Ganti logika baris 121 - 130 menjadi:
    $nis = $this->request->getPost('nis');
    $mataPelajaran = $this->request->getPost('mata_pelajaran');
    $idGuru = session()->get('ref_id');

    $dbSiswa = $siswaModel->where('nis', $nis)->first();
    if (!$dbSiswa) {
        return redirect()->back()->withInput()->with('error', 'Siswa tidak ditemukan.');
    }

    // Jika admin yang login, cari id_guru secara otomatis dari tabel guru_mapel_kelas
    if (session()->get('role') === 'admin') {
        $db = \Config\Database::connect();
        $mapping = $db->table('guru_mapel_kelas')
            ->where('mata_pelajaran', $mataPelajaran)
            ->where('kelas', $dbSiswa['kelas'])
            ->get()
            ->getRowArray();
        if ($mapping) {
            $idGuru = $mapping['id_guru'];
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal! Tidak ada guru yang terdaftar mengampu mapel "' . $mataPelajaran . '" di kelas ' . $dbSiswa['kelas']);
        }
    }

    $dbGuru = $guruModel->find($idGuru);
    if (!$dbGuru) {
        return redirect()->back()->withInput()->with('error', 'Guru pengampu tidak ditemukan.');
    }
    ```
*   **Kode Modifikasi pada `update()` (Ubah dengan logika penentu `$idGuru` yang sama seperti di atas):**
    ```php
    // Ganti baris penentuan $idGuru pada method update() menjadi:
    $idGuru = session()->get('ref_id');
    if (session()->get('role') === 'admin') {
        $db = \Config\Database::connect();
        $mapping = $db->table('guru_mapel_kelas')
            ->where('mata_pelajaran', $mataPelajaran)
            ->where('kelas', $dbSiswa['kelas'])
            ->get()
            ->getRowArray();
        if ($mapping) {
            $idGuru = $mapping['id_guru'];
        } else {
            $idGuru = $nilai['id_guru']; // Fallback ke id guru lama jika mapping baru tidak ditemukan
        }
    }
    ```

---

## 2. PENENTUAN GRADE NILAI (A, B, C, D, E)
*   **Tujuan:** Menambahkan kolom Grade berdasarkan nilai akhir pada tampilan Rapor dan daftar nilai.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Helpers/structured_helper.php](file:///c:/xampp/htdocs/sistem-nilai/app/Helpers/structured_helper.php)
    *   **Tambah** fungsi baru di paling bawah file:
        ```php
        if (!function_exists('tentukan_grade')) {
            function tentukan_grade($nilai) {
                if ($nilai >= 90) return 'A';
                if ($nilai >= 80) return 'B';
                if ($nilai >= 70) return 'C';
                if ($nilai >= 60) return 'D';
                return 'E';
            }
        }
        ```

2.  **Buka file:** [app/Views/laporan/rapor.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/laporan/rapor.php)
    *   **Ubah** tabel nilai dengan menambahkan kolom Grade.
    *   *Kode Modifikasi (Tabel Header):*
        ```html
        <!-- Cari tag <thead> pada baris 53, ganti/tambahkan <th> Grade sebelum th Nilai Akhir atau setelahnya -->
        <th>Nilai Akhir</th>
        <th>Grade</th>
        ```
    *   *Kode Modifikasi (Tabel Body):*
        ```html
        <!-- Cari tag <tbody> baris 74, tambahkan kolom grade setelah nilai_akhir -->
        <td style="font-weight: 700; color: var(--primary);"><?= number_format($n['nilai_akhir'], 2) ?></td>
        <td style="font-weight: 700; text-align: center;"><?= tentukan_grade($n['nilai_akhir']) ?></td>
        ```

---

## 3. BATAS KKM DINAMIS PER GURU / MATA PELAJARAN
*   **Tujuan:** Mengganti batas KKM statis (75) menjadi dinamis berdasarkan kolom KKM pada profil guru.

### Langkah-langkah Pembuatan:
1.  **Jalankan SQL untuk menambahkan kolom KKM di database:**
    ```sql
    ALTER TABLE guru ADD COLUMN kkm INT NOT NULL DEFAULT 75;
    ```

2.  **Buka file:** [app/Helpers/structured_helper.php](file:///c:/xampp/htdocs/sistem-nilai/app/Helpers/structured_helper.php)
    *   **Ubah** fungsi `tentukan_status_kelulusan` agar menerima parameter batas KKM dinamis.
        ```php
        function tentukan_status_kelulusan($nilai_akhir, $kkm = 75) {
            return $nilai_akhir >= $kkm ? 'Lulus' : 'Tidak Lulus';
        }
        ```

3.  **Buka file:** [app/OOP/Nilai.php](file:///c:/xampp/htdocs/sistem-nilai/app/OOP/Nilai.php)
    *   **Ubah** constructor dan method `kalkulasi()` agar dapat menerima properti KKM dari profil Guru.
        ```php
        // Tambahkan atribut baru:
        private int $kkm;

        // Ubah Constructor:
        public function __construct(
            ?int $id,
            Siswa $siswa,
            Guru $guru,
            float $nilaiTugas,
            float $nilaiUts,
            float $nilaiUas,
            int $kkm = 75 // Tambah parameter ini
        ) {
            $this->loadHelper();
            $this->id = $id;
            $this->siswa = $siswa;
            $this->guru = $guru;
            $this->nilaiTugas = $nilaiTugas;
            $this->nilaiUts = $nilaiUts;
            $this->nilaiUas = $nilaiUas;
            $this->kkm = $kkm; // Set properti kkm

            $this->kalkulasi();
        }

        private function kalkulasi(): void {
            $this->loadHelper();
            $this->nilaiAkhir = hitung_nilai_akhir($this->nilaiTugas, $this->nilaiUts, $this->nilaiUas);
            // Kirim parameter KKM dinamis ke helper:
            $this->statusKelulusan = tentukan_status_kelulusan($this->nilaiAkhir, $this->kkm);
        }
        ```

---

## 4. FITUR PENCARIAN & PAGINATION DATA SISWA (ADMIN)
*   **Tujuan:** Menambahkan bar pencarian nama siswa di tabel data master admin dan membagi tampilan data per 5 atau 10 baris.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Controllers/SiswaController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/SiswaController.php)
    *   **Ubah** method `index()` untuk memproses pencarian dan pagination:
        ```php
        public function index()
        {
            $siswaModel = new SiswaModel();
            $keyword = $this->request->getGet('keyword');
            
            if (!empty($keyword)) {
                $siswaModel->like('nama', $keyword)->orLike('nis', $keyword);
            }

            $data = [
                'title' => 'Daftar Siswa',
                'siswa' => $siswaModel->paginate(10, 'siswa'), // Menggunakan paginate bawaan CI4
                'pager' => $siswaModel->pager,
                'keyword' => $keyword
            ];
            return view('siswa/index', $data);
        }
        ```

2.  **Buka file:** [app/Views/siswa/index.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/siswa/index.php)
    *   **Tambah** form pencarian HTML di atas tabel:
        ```html
        <form action="" method="get" style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
            <input type="text" name="keyword" placeholder="Cari NIS atau Nama Siswa..." class="form-control" value="<?= esc($keyword ?? '') ?>" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if (!empty($keyword)): ?>
                <a href="<?= base_url('/siswa') ?>" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
        ```
    *   **Tambah** link pagination di bawah tabel:
        ```html
        <div style="margin-top: 1rem;">
            <?= $pager->links('siswa', 'default_full') ?>
        </div>
        ```

---

## 5. EKSPOR REKAP NILAI KE EXCEL (FORMAT CSV)
*   **Tujuan:** Menyediakan tombol bagi Guru/Admin untuk mengunduh seluruh rangkuman nilai kelas dalam format file `.csv`.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Config/Routes.php](file:///c:/xampp/htdocs/sistem-nilai/app/Config/Routes.php)
    *   **Tambah** route untuk memicu download CSV:
        ```php
        $routes->get('laporan/export', 'LaporanController::exportCsv');
        ```

2.  **Buka file:** [app/Controllers/LaporanController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/LaporanController.php)
    *   **Tambah** method baru `exportCsv()`:
        ```php
        public function exportCsv()
        {
            $siswaModel = new SiswaModel();
            $nilaiModel = new NilaiModel();
            $siswaList = $siswaModel->findAll();

            // Set Header agar file langsung terdownload sebagai CSV
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="rekap_nilai_siswa.csv"');
            
            $output = fopen('php://output', 'w');
            // Menulis kolom header CSV
            fputcsv($output, ['NIS', 'Nama Siswa', 'Kelas', 'Jumlah Mapel Diikuti', 'Nilai Rata-rata', 'Status']);

            foreach ($siswaList as $siswa) {
                $grades = $nilaiModel->where('nis', $siswa['nis'])->findAll();
                $jumlahMapel = count($grades);
                $rataRata = 0.0;
                if ($jumlahMapel > 0) {
                    $total = array_sum(array_column($grades, 'nilai_akhir'));
                    $rataRata = $total / $jumlahMapel;
                }
                $status = ($rataRata >= 75) ? 'Lulus' : 'Tidak Lulus';
                if ($jumlahMapel === 0) $status = 'Belum Ada Nilai';

                fputcsv($output, [
                    $siswa['nis'],
                    $siswa['nama'],
                    $siswa['kelas'],
                    $jumlahMapel,
                    round($rataRata, 2),
                    $status
                ]);
            }
            fclose($output);
            exit();
        }
        ```

3.  **Buka file:** [app/Views/laporan/index.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/laporan/index.php)
    *   **Tambah** tombol "Ekspor CSV" di atas tabel rekapitulasi:
        ```html
        <a href="<?= base_url('/laporan/export') ?>" class="btn btn-success" style="display: inline-flex; align-items: center; gap: 0.25rem;">
            <i class="fa-solid fa-file-excel"></i>
            <span>Ekspor CSV</span>
        </a>
        ```

---

## 6. VALIDASI BLOKIR LOGIN SISWA TIDAK AKTIF
*   **Tujuan:** Mencegah siswa yang statusnya bukan "Aktif" (misalnya "Lulus", "Diberhentikan", atau "Pindah") untuk masuk/login ke sistem.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Controllers/AuthController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/AuthController.php)
    *   **Ubah** method `loginProcess()` untuk memvalidasi status keaktifan sebelum menyimpan session:
        ```php
        // Cari baris 45-53 (pengecekan status siswa), ganti/sisipkan logika berikut:
        if ($user['role'] === 'siswa') {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->find($user['ref_id']);
            if (!$siswa || strtolower($siswa['status_siswa']) !== 'aktif') {
                return redirect()->back()->withInput()->with('error', 'Login ditolak. Status siswa Anda tidak aktif.');
            }
            $foto = $siswa['foto'];
        }
        ```

---

## 7. PENGURUTAN RANKING SISWA DI REKAP KELAS
*   **Tujuan:** Mengurutkan daftar nilai siswa berdasarkan rata-rata nilai tertinggi ke terendah secara otomatis.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Controllers/LaporanController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/LaporanController.php)
    *   **Ubah** akhir method `index()` sebelum melempar data ke view untuk mengurutkan array hasil rekapitulasi:
        ```php
        // Letakkan tepat di baris sebelum array $data didefinisikan (Baris 48-49):
        usort($rekapSiswa, function($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata']; // Urutan descending
        });
        ```

---

## 8. CEK DUPLIKAT NILAI (SATU MAPEL SATU NILAI PER SISWA)
*   **Tujuan:** Mencegah guru menginput nilai ganda untuk siswa yang sama pada mata pelajaran yang diampunya.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Controllers/NilaiController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/NilaiController.php)
    *   **Ubah** method `store()` untuk melakukan pengecekan duplikasi nilai sebelum melakukan instansiasi objek OOP:
        ```php
        // Cari baris 77 (sebelum blok try {}), tambahkan kode pengecekan ini:
        $nilaiDuplikat = $nilaiModel->where('nis', $nis)->where('id_guru', $idGuru)->first();
        if ($nilaiDuplikat) {
            return redirect()->back()->withInput()->with('error', 'Gagal! Nilai mata pelajaran Anda sudah diinput untuk siswa ini.');
        }
        ```

---

## 9. PENAMBAHAN TANGGAL TINJAU PADA BANDING NILAI
*   **Tujuan:** Mencatat dan menampilkan tanggal/waktu saat Guru memberikan keputusan persetujuan atas protes nilai siswa.

### Langkah-langkah Pembuatan:
1.  **Jalankan perintah SQL ini untuk memperbarui tabel:**
    ```sql
    ALTER TABLE banding_nilai ADD COLUMN tanggal_tinjau DATETIME DEFAULT NULL;
    ```

2.  **Buka file:** [app/Controllers/BandingController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/BandingController.php)
    *   **Ubah** method `tinjauUpdate()` untuk mencatat tanggal peninjauan saat guru mengirim form keputusan:
        ```php
        // Cari baris 128, tambahkan kolom tanggal_tinjau ke dalam array update:
        $bandingModel->update($id, [
            'status'          => $this->request->getPost('status'),
            'keterangan_guru' => $this->request->getPost('keterangan_guru'),
            'tanggal_tinjau'  => date('Y-m-d H:i:s') // Tambahkan baris ini
        ]);
        ```

3.  **Buka file:** [app/Views/banding/riwayat.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/banding/riwayat.php)
    *   **Ubah** baris detail riwayat banding siswa agar memunculkan tanggal tinjauan guru.
    *   *Kode Tambahan (Tampilkan di samping status banding):*
        ```html
        <?php if (!empty($r['tanggal_tinjau'])): ?>
            <small style="color: var(--text-muted);">Ditinjau pada: <?= date('d-m-Y H:i', strtotime($r['tanggal_tinjau'])) ?></small>
        <?php endif; ?>
        ```

---

## 10. EDIT & PENGGANTIAN FOTO PROFIL SISWA OLEH ADMIN
*   **Tujuan:** Memberikan wewenang kepada Admin agar dapat memperbarui foto profil siswa saat mengedit data siswa di panel Admin.

### Langkah-langkah Pembuatan:
1.  **Buka file:** [app/Views/siswa/edit.php](file:///c:/xampp/htdocs/sistem-nilai/app/Views/siswa/edit.php)
    *   **Ubah** form tag agar mendukung pengiriman file:
        ```html
        <!-- Ganti tag form pembuka agar menyertakan enctype -->
        <form action="<?= base_url('/siswa/update/' . $siswa['nis']) ?>" method="post" enctype="multipart/form-data">
        ```
    *   **Tambah** input file untuk memilih foto baru:
        ```html
        <div class="form-group" style="margin-top: 1rem;">
            <label for="foto">Foto Profil Baru (Opsional)</label>
            <input type="file" name="foto" id="foto" class="form-control">
            <small style="color: var(--text-muted);">Format: JPG/PNG, Maksimal: 2MB</small>
        </div>
        ```

2.  **Buka file:** [app/Controllers/SiswaController.php](file:///c:/xampp/htdocs/sistem-nilai/app/Controllers/SiswaController.php)
    *   **Ubah** method `update()` agar memproses unggahan foto baru jika dipilih:
        ```php
        public function update($nis)
        {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->find($nis);
            if (!$siswa) {
                return redirect()->to('/siswa')->with('error', 'Siswa tidak ditemukan.');
            }

            $rules = [
                'nama'          => 'required|min_length[2]|max_length[100]',
                'kelas'         => 'required|in_list[1,2,3,4,5,6]',
                'jenis_kelamin' => 'required|in_list[L,P]'
            ];

            // Validasi file foto jika diunggah
            $fotoFile = $this->request->getFile('foto');
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $fotoName = $siswa['foto'];
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $uploadPath = ROOTPATH . 'public/uploads/foto/';
                // Hapus foto lama jika ada
                if (!empty($siswa['foto']) && file_exists($uploadPath . $siswa['foto'])) {
                    @unlink($uploadPath . $siswa['foto']);
                }
                $fotoName = $fotoFile->getRandomName();
                $fotoFile->move($uploadPath, $fotoName);
            }

            $siswaModel->update($nis, [
                'nama'          => $this->request->getPost('nama'),
                'kelas'         => $this->request->getPost('kelas'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'foto'          => $fotoName
            ]);

            return redirect()->to('/siswa')->with('success', 'Data siswa berhasil diperbarui oleh Admin.');
        }
        ```
