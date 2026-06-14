<?php

namespace App\Controllers;

use App\Models\NilaiModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;
use App\OOP\Siswa;
use App\OOP\Guru;
use App\OOP\Nilai;

class NilaiController extends BaseController
{
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
            $nilai = $nilaiModel->getNilaiWithDetails();
        }

        $data = [
            'title' => 'Daftar Nilai Siswa',
            'nilai' => $nilai,
            'kelasDiajar' => $kelasDiajar,
            'mapelDiajar' => $mapelDiajar
        ];
        return view('nilai/index', $data);
    }

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

    /**
     * AJAX endpoint: get students by class (filtered by teacher's assigned classes).
     */
    public function getSiswaByKelas($kelas)
    {
        $siswaModel = new SiswaModel();
        $role = session()->get('role');
        $idGuru = session()->get('ref_id');

        // Security: only allow classes that the guru teaches
        if ($role === 'guru') {
            $db = \Config\Database::connect();
            $mappingExists = $db->table('guru_mapel_kelas')
                ->where('id_guru', $idGuru)
                ->where('kelas', $kelas)
                ->countAllResults();
            if ($mappingExists === 0) {
                return $this->response->setJSON([]);
            }
        }

        $siswa = $siswaModel->where('status_siswa', 'Aktif')->where('kelas', $kelas)->orderBy('nama', 'ASC')->findAll();
        return $this->response->setJSON($siswa);
    }

    public function store()
    {
        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();
        $nilaiModel = new NilaiModel();

        $rules = [
            'nis'             => 'required',
            'mata_pelajaran'  => 'required',
            'nilai_tugas'     => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_uts'       => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_uas'       => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nis = $this->request->getPost('nis');
        $mataPelajaran = $this->request->getPost('mata_pelajaran');
        $idGuru = session()->get('ref_id');
        
        $dbSiswa = $siswaModel->where('nis', $nis)->first();
        $dbGuru = $guruModel->find($idGuru);

        if (!$dbSiswa || !$dbGuru) {
            return redirect()->back()->withInput()->with('error', 'Siswa atau Guru tidak ditemukan.');
        }

        // Validate that this teacher actually teaches this subject in this student's class
        $db = \Config\Database::connect();
        $mappingExists = $db->table('guru_mapel_kelas')
            ->where('id_guru', $idGuru)
            ->where('mata_pelajaran', $mataPelajaran)
            ->where('kelas', $dbSiswa['kelas'])
            ->countAllResults();
        if ($mappingExists === 0) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak mengajar mata pelajaran "' . $mataPelajaran . '" di kelas ' . $dbSiswa['kelas'] . '.');
        }

        // Validate: no duplicate (same nis + same guru + same mapel)
        $existing = $nilaiModel->where('nis', $nis)
            ->where('id_guru', $idGuru)
            ->where('mata_pelajaran', $mataPelajaran)
            ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Nilai untuk siswa ini pada mata pelajaran "' . $mataPelajaran . '" sudah ada. Silakan edit nilai yang sudah ada.');
        }

        try {
            // Instansiasi objek domain OOP
            $siswaObj = new Siswa($dbSiswa['nis'], $dbSiswa['nama'], $dbSiswa['kelas']);
            $guruObj = new Guru($dbGuru['id_guru'], $dbGuru['nama_guru'], $dbGuru['mata_pelajaran']);
            
            $nilaiObj = new Nilai(
                null,
                $siswaObj,
                $guruObj,
                $mataPelajaran,
                (float)$this->request->getPost('nilai_tugas'),
                (float)$this->request->getPost('nilai_uts'),
                (float)$this->request->getPost('nilai_uas')
            );

            // Simpan ke database menggunakan representasi array objek OOP
            $nilaiModel->save($nilaiObj->toArray());

            return redirect()->to('/nilai')->with('success', 'Data nilai ' . $mataPelajaran . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $nilaiModel = new NilaiModel();
        $siswaModel = new SiswaModel();

        $nilai = $nilaiModel->find($id);

        if (!$nilai) {
            return redirect()->to('/nilai')->with('error', 'Data nilai tidak ditemukan.');
        }

        // Authorization check: guru only edits their own grades
        if (session()->get('role') === 'guru' && $nilai['id_guru'] != session()->get('ref_id')) {
            return redirect()->to('/nilai')->with('error', 'Akses ditolak. Anda tidak diperkenankan mengedit nilai dari guru lain.');
        }

        $data = [
            'title'      => 'Edit Nilai Siswa',
            'nilai'      => $nilai,
            'siswa'      => $siswaModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('nilai/edit', $data);
    }

    public function update($id)
    {
        $nilaiModel = new NilaiModel();
        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();

        $nilai = $nilaiModel->find($id);

        if (!$nilai) {
            return redirect()->to('/nilai')->with('error', 'Data nilai tidak ditemukan.');
        }

        // Authorization check: guru only updates their own grades
        if (session()->get('role') === 'guru' && $nilai['id_guru'] != session()->get('ref_id')) {
            return redirect()->to('/nilai')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nis'         => 'required',
            'nilai_tugas' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_uts'   => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_uas'   => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nis = $this->request->getPost('nis');
        $idGuru = session()->get('ref_id');
        // Use the existing mata_pelajaran from the record (not editable)
        $mataPelajaran = $nilai['mata_pelajaran'];
        
        $dbSiswa = $siswaModel->where('nis', $nis)->first();
        $dbGuru = $guruModel->find($idGuru);

        if (!$dbSiswa || !$dbGuru) {
            return redirect()->back()->withInput()->with('error', 'Siswa atau Guru tidak ditemukan.');
        }

        try {
            // Instansiasi objek domain OOP
            $siswaObj = new Siswa($dbSiswa['nis'], $dbSiswa['nama'], $dbSiswa['kelas']);
            $guruObj = new Guru($dbGuru['id_guru'], $dbGuru['nama_guru'], $dbGuru['mata_pelajaran']);
            
            $nilaiObj = new Nilai(
                $id,
                $siswaObj,
                $guruObj,
                $mataPelajaran,
                (float)$this->request->getPost('nilai_tugas'),
                (float)$this->request->getPost('nilai_uts'),
                (float)$this->request->getPost('nilai_uas')
            );

            // Simpan ke database menggunakan representasi array objek OOP
            $nilaiModel->update($id, $nilaiObj->toArray());

            return redirect()->to('/nilai')->with('success', 'Data nilai berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $nilaiModel = new NilaiModel();
        $nilai = $nilaiModel->find($id);

        if (!$nilai) {
            return redirect()->to('/nilai')->with('error', 'Data nilai tidak ditemukan.');
        }

        // Authorization check: guru only deletes their own grades
        if (session()->get('role') === 'guru' && $nilai['id_guru'] != session()->get('ref_id')) {
            return redirect()->to('/nilai')->with('error', 'Akses ditolak.');
        }

        $nilaiModel->delete($id);
        return redirect()->to('/nilai')->with('success', 'Data nilai berhasil dihapus.');
    }
}
