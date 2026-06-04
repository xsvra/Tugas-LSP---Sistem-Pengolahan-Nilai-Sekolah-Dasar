<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\UserModel;

class GuruController extends BaseController
{
    public function index()
    {
        $guruModel = new GuruModel();
        $data = [
            'title' => 'Daftar Guru',
            'guru'  => $guruModel->findAll()
        ];
        return view('guru/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COALESCE(MAX(id_guru), 0) + 1 AS next_id FROM guru");
        $nextId = $query->getRow()->next_id;

        $data = [
            'title'      => 'Tambah Guru',
            'nextId'     => $nextId,
            'validation' => \Config\Services::validation()
        ];
        return view('guru/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $guruModel = new GuruModel();
        $userModel = new UserModel();

        $rules = [
            'username'            => 'required|alpha_numeric_space|min_length[3]|max_length[50]|is_unique[users.username]',
            'password'            => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password]',
            'mata_pelajaran'      => 'required|min_length[2]|max_length[100]',
            'status_kepegawaian'  => 'required',
            'tanggal_masuk'       => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        $db->transBegin();

        // 1. Simpan Guru
        $guruModel->save([
            'nik'                 => '',
            'nama_guru'           => $username, // Default name, guru will edit in profile
            'jenis_kelamin'       => 'L',
            'mata_pelajaran'      => $this->request->getPost('mata_pelajaran'),
            'status_kepegawaian'  => $this->request->getPost('status_kepegawaian'),
            'tanggal_masuk'       => $this->request->getPost('tanggal_masuk')
        ]);

        $id_guru = $db->insertID();

        // 2. Simpan User
        $userModel->save([
            'username' => $username,
            'password' => $password,
            'role'     => 'guru',
            'ref_id'   => $id_guru
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan guru.');
        }

        $db->transCommit();
        return redirect()->to('/guru')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit($id_guru)
    {
        $guruModel = new GuruModel();
        $guru = $guruModel->find($id_guru);

        if (!$guru) {
            return redirect()->to('/guru')->with('error', 'Data guru tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Guru',
            'guru'      => $guru,
            'validation' => \Config\Services::validation()
        ];
        return view('guru/edit', $data);
    }

    public function update($id_guru)
    {
        $guruModel = new GuruModel();
        $guru = $guruModel->find($id_guru);

        if (!$guru) {
            return redirect()->to('/guru')->with('error', 'Data guru tidak ditemukan.');
        }

        $rules = [
            'nik'                => 'required|numeric|min_length[10]|max_length[20]',
            'nama_guru'          => 'required|min_length[2]|max_length[100]',
            'jenis_kelamin'      => 'required|in_list[L,P]',
            'mata_pelajaran'     => 'required|min_length[2]|max_length[100]',
            'status_kepegawaian' => 'required',
            'tanggal_masuk'      => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $guruModel->update($id_guru, [
            'nik'                => $this->request->getPost('nik'),
            'nama_guru'          => $this->request->getPost('nama_guru'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'mata_pelajaran'     => $this->request->getPost('mata_pelajaran'),
            'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
            'tanggal_masuk'      => $this->request->getPost('tanggal_masuk')
        ]);

        return redirect()->to('/guru')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function delete($id_guru)
    {
        $guruModel = new GuruModel();
        $guru = $guruModel->find($id_guru);

        if (!$guru) {
            return redirect()->to('/guru')->with('error', 'Data guru tidak ditemukan.');
        }

        $guruModel->delete($id_guru);
        return redirect()->to('/guru')->with('success', 'Data guru berhasil dihapus.');
    }

    /**
     * Halaman profil untuk guru mengelola data diri mandiri
     */
    public function profil()
    {
        $guruModel = new GuruModel();
        $id_guru = session()->get('ref_id');
        $guru = $guruModel->find($id_guru);

        if (!$guru) {
            return redirect()->to('/')->with('error', 'Data guru tidak ditemukan.');
        }

        $data = [
            'title'      => 'Data Diri Guru',
            'guru'       => $guru,
            'editMode'   => $this->request->getGet('edit') === 'true',
            'validation' => \Config\Services::validation()
        ];
        return view('guru/profil', $data);
    }

    /**
     * Memproses update data diri dari guru
     */
    public function profilUpdate()
    {
        $guruModel = new GuruModel();
        $id_guru = session()->get('ref_id');
        $guru = $guruModel->find($id_guru);

        if (!$guru) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_guru'           => 'required|min_length[2]|max_length[100]',
            'nik'                 => 'required|numeric|min_length[10]|max_length[20]',
            'jenis_kelamin'       => 'required|in_list[L,P]',
            'tempat_lahir'        => 'permit_empty|max_length[100]',
            'tanggal_lahir'       => 'permit_empty|valid_date[Y-m-d]',
            'alamat'              => 'permit_empty',
            'no_hp'               => 'permit_empty|max_length[20]',
            'email'               => 'permit_empty|valid_email|max_length[100]',
            'mata_pelajaran'      => 'required|min_length[2]|max_length[100]',
            'pendidikan_terakhir' => 'permit_empty|max_length[50]',
            'status_kepegawaian'  => 'permit_empty|max_length[50]',
            'tanggal_masuk'       => 'permit_empty|valid_date[Y-m-d]'
        ];

        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fotoName = $guru['foto'];
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/uploads/foto/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            // Delete old photo if exists
            if (!empty($guru['foto']) && file_exists($uploadPath . $guru['foto'])) {
                @unlink($uploadPath . $guru['foto']);
            }

            $fotoName = $fotoFile->getRandomName();
            $fotoFile->move($uploadPath, $fotoName);
        }

        $guruModel->update($id_guru, [
            'nama_guru'           => $this->request->getPost('nama_guru'),
            'nik'                 => $this->request->getPost('nik'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir') ?: null,
            'alamat'              => $this->request->getPost('alamat'),
            'no_hp'               => $this->request->getPost('no_hp'),
            'email'               => $this->request->getPost('email'),
            'mata_pelajaran'      => $this->request->getPost('mata_pelajaran'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'status_kepegawaian'  => $this->request->getPost('status_kepegawaian'),
            'tanggal_masuk'       => $this->request->getPost('tanggal_masuk') ?: null,
            'foto'                => $fotoName
        ]);

        // Update session photo & name
        session()->set([
            'foto'         => $fotoName,
            'nama_lengkap' => $this->request->getPost('nama_guru')
        ]);

        return redirect()->to('/profil/guru')->with('success', 'Data diri berhasil diperbarui.');
    }
}
