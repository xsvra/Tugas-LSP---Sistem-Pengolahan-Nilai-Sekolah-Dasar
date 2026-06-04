<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\UserModel;

class SiswaController extends BaseController
{
    public function index()
    {
        $siswaModel = new SiswaModel();
        $data = [
            'title' => 'Daftar Siswa',
            'siswa' => $siswaModel->findAll()
        ];
        return view('siswa/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COALESCE(MAX(CAST(nis AS UNSIGNED)), 100) + 1 AS next_nis FROM siswa");
        $nextNis = $query->getRow()->next_nis;

        $data = [
            'title' => 'Tambah Siswa',
            'nextNis' => $nextNis,
            'validation' => \Config\Services::validation()
        ];
        return view('siswa/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $siswaModel = new SiswaModel();
        $userModel = new UserModel();

        $rules = [
            'nis'                 => 'required|min_length[3]|max_length[20]|is_unique[siswa.nis]',
            'username'            => 'required|alpha_numeric_space|min_length[3]|max_length[50]|is_unique[users.username]',
            'password'            => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password]',
            'kelas'               => 'required|in_list[1,2,3,4,5,6]',
            'status_siswa'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nis = $this->request->getPost('nis');
        $username = $this->request->getPost('username');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        $db->transBegin();

        // 1. Simpan Siswa
        $siswaModel->save([
            'nis'           => $nis,
            'nisn'          => '0000000000',
            'nama'          => $username, // Default name is username
            'kelas'         => $this->request->getPost('kelas'),
            'jenis_kelamin' => 'L', // Default L
            'status_siswa'  => $this->request->getPost('status_siswa')
        ]);

        // 2. Simpan User
        $userModel->save([
            'username' => $username,
            'password' => $password,
            'role'     => 'siswa',
            'ref_id'   => $nis
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan siswa.');
        }

        $db->transCommit();
        return redirect()->to('/siswa')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit($nis)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->to('/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Siswa',
            'siswa'      => $siswa,
            'validation' => \Config\Services::validation()
        ];
        return view('siswa/edit', $data);
    }

    public function update($nis)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->to('/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $rules = [
            'nama'          => 'required|min_length[2]|max_length[100]',
            'kelas'         => 'required|in_list[1,2,3,4,5,6]',
            'status_siswa'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $siswaModel->update($siswa['id_siswa'], [
            'nama'          => $this->request->getPost('nama'),
            'kelas'         => $this->request->getPost('kelas'),
            'status_siswa'  => $this->request->getPost('status_siswa')
        ]);

        return redirect()->to('/siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function delete($nis)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->to('/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $siswaModel->delete($siswa['id_siswa']);
        $db->table('users')->where('ref_id', $nis)->where('role', 'siswa')->delete();

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('/siswa')->with('error', 'Gagal menghapus data siswa.');
        }

        $db->transCommit();
        return redirect()->to('/siswa')->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Halaman profil untuk siswa mengelola data diri mandiri
     */
    public function profil()
    {
        $siswaModel = new SiswaModel();
        $nis = session()->get('ref_id');
        $siswa = $siswaModel->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->to('/')->with('error', 'Data siswa tidak ditemukan.');
        }

        $data = [
            'title'      => 'Data Diri Siswa',
            'siswa'      => $siswa,
            'editMode'   => $this->request->getGet('edit') === 'true',
            'validation' => \Config\Services::validation()
        ];
        return view('siswa/profil', $data);
    }

    /**
     * Memproses update data diri dari siswa
     */
    public function profilUpdate()
    {
        $siswaModel = new SiswaModel();
        $nis = session()->get('ref_id');
        $siswa = $siswaModel->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama'          => 'required|min_length[2]|max_length[100]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir'  => 'permit_empty|max_length[100]',
            'tanggal_lahir' => 'permit_empty|valid_date[Y-m-d]',
            'alamat'        => 'permit_empty',
            'kelas'         => 'required|in_list[1,2,3,4,5,6]',
            'tahun_masuk'   => 'permit_empty|exact_length[4]|numeric',
            'nama_wali'     => 'permit_empty|max_length[100]',
            'no_hp_wali'    => 'permit_empty|max_length[20]',
            'status_siswa'  => 'permit_empty|max_length[50]'
        ];

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
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Delete old photo if exists
            if (!empty($siswa['foto']) && file_exists($uploadPath . $siswa['foto'])) {
                @unlink($uploadPath . $siswa['foto']);
            }

            $fotoName = $fotoFile->getRandomName();
            $fotoFile->move($uploadPath, $fotoName);
        }

        $siswaModel->update($siswa['id_siswa'], [
            'nama'          => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'  => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'alamat'        => $this->request->getPost('alamat'),
            'kelas'         => $this->request->getPost('kelas'),
            'tahun_masuk'   => $this->request->getPost('tahun_masuk'),
            'nama_wali'     => $this->request->getPost('nama_wali'),
            'no_hp_wali'    => $this->request->getPost('no_hp_wali'),
            'status_siswa'  => $this->request->getPost('status_siswa') ?: 'Aktif',
            'foto'          => $fotoName
        ]);

        // Update session photo & name
        session()->set([
            'foto'         => $fotoName,
            'nama_lengkap' => $this->request->getPost('nama')
        ]);

        return redirect()->to('/profil/siswa')->with('success', 'Data diri berhasil diperbarui.');
    }
}
