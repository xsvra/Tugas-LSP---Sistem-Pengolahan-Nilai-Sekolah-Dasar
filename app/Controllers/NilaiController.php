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

        if ($role === 'guru') {
            $nilai = $nilaiModel->select('nilai.*, siswa.nama as nama_siswa, siswa.kelas, guru.nama_guru, guru.mata_pelajaran')
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
            'nilai' => $nilai
        ];
        return view('nilai/index', $data);
    }

    public function create()
    {
        $siswaModel = new SiswaModel();

        $data = [
            'title'      => 'Input Nilai Siswa',
            'siswa'      => $siswaModel->where('status_siswa', 'Aktif')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('nilai/create', $data);
    }

    public function store()
    {
        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();
        $nilaiModel = new NilaiModel();

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
                null,
                $siswaObj,
                $guruObj,
                (float)$this->request->getPost('nilai_tugas'),
                (float)$this->request->getPost('nilai_uts'),
                (float)$this->request->getPost('nilai_uas')
            );

            // Simpan ke database menggunakan representasi array objek OOP
            $nilaiModel->save($nilaiObj->toArray());

            return redirect()->to('/nilai')->with('success', 'Data nilai berhasil ditambahkan.');
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
