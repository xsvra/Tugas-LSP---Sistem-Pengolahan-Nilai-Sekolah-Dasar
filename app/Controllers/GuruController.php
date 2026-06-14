<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\UserModel;

class GuruController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $guru = $db->table('guru')
            ->select('guru.*, guru_mapel_kelas.mata_pelajaran as mapel_mapped, guru_mapel_kelas.kelas as kelas_mapped')
            ->join('guru_mapel_kelas', 'guru_mapel_kelas.id_guru = guru.id_guru', 'left')
            ->orderBy('guru.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Daftar Guru',
            'guru'  => $guru
        ];
        return view('guru/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Guru',
            'nextId'     => '(Otomatis)',
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
            'status_kepegawaian'  => 'required',
            'tanggal_masuk'       => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mapped_mapels = $this->request->getPost('mapped_mapel') ?: [];
        $mapped_kelases = $this->request->getPost('mapped_kelas') ?: [];

        $errors = [];
        if (empty($mapped_mapels) || empty($mapped_kelases) || count($mapped_mapels) !== count($mapped_kelases)) {
            $errors['mappings'] = 'Pemetaan mata pelajaran dan kelas harus diisi minimal satu.';
        } else {
            $seen = [];
            for ($i = 0; $i < count($mapped_mapels); $i++) {
                $mapel = $mapped_mapels[$i];
                $kelas = $mapped_kelases[$i];
                if (empty($mapel) || empty($kelas)) {
                    $errors['mappings'] = 'Mata pelajaran dan kelas tidak boleh kosong.';
                    break;
                }
                $key = $mapel . '-' . $kelas;
                if (in_array($key, $seen)) {
                    $errors['mappings'] = 'Pemetaan untuk ' . $mapel . ' di Kelas ' . $kelas . ' duplikat dalam form.';
                    break;
                }
                $seen[] = $key;

                $existing = $db->table('guru_mapel_kelas')
                    ->select('guru_mapel_kelas.*, guru.nama_guru')
                    ->join('guru', 'guru.id_guru = guru_mapel_kelas.id_guru')
                    ->where('guru_mapel_kelas.mata_pelajaran', $mapel)
                    ->where('guru_mapel_kelas.kelas', $kelas)
                    ->get()
                    ->getRow();
                if ($existing) {
                    $errors['mappings'] = 'Mata pelajaran ' . $mapel . ' di Kelas ' . $kelas . ' sudah diajar oleh guru ' . $existing->nama_guru . '.';
                    break;
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $unique_mapels = array_unique($mapped_mapels);
        $unique_kelases = array_unique($mapped_kelases);
        $mata_pelajaran_str = implode(',', $unique_mapels);
        $kelas_diajar_str = implode(',', $unique_kelases);

        $username = $this->request->getPost('username');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        $status_kepegawaian = $this->request->getPost('status_kepegawaian');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');

        $year2 = date('y', strtotime($tanggal_masuk));
        $statusMap = [
            'PNS' => '01',
            'Honorer' => '02',
            'P3K' => '03'
        ];
        $statusKey = strtoupper($status_kepegawaian);
        $statusCode = isset($statusMap[$statusKey]) ? $statusMap[$statusKey] : '00';
        $prefix = $year2 . $statusCode;

        $query = $db->query("SELECT id_guru FROM guru WHERE CAST(id_guru AS CHAR) LIKE '$prefix%' AND LENGTH(CAST(id_guru AS CHAR)) = 8 ORDER BY id_guru DESC LIMIT 1");
        $row = $query->getRow();
        if ($row) {
            $lastSeq = intval(substr($row->id_guru, 4));
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }
        $id_guru = intval($prefix . sprintf('%04d', $nextSeq));

        $db->transBegin();

        // 1. Simpan Guru
        $guruModel->insert([
            'id_guru'             => $id_guru,
            'nik'                 => '',
            'nama_guru'           => $username, // Default name, guru will edit in profile
            'jenis_kelamin'       => 'L',
            'mata_pelajaran'      => $mata_pelajaran_str,
            'kelas_diajar'        => $kelas_diajar_str,
            'status_kepegawaian'  => $status_kepegawaian,
            'tanggal_masuk'       => $tanggal_masuk
        ]);

        $id_guru = $db->affectedRows() > 0 ? $id_guru : null;

        // 2. Simpan User
        $userModel->save([
            'username' => $username,
            'password' => $password,
            'role'     => 'guru',
            'ref_id'   => $id_guru
        ]);

        // 3. Simpan Pemetaan Mapel & Kelas
        for ($i = 0; $i < count($mapped_mapels); $i++) {
            $db->table('guru_mapel_kelas')->insert([
                'id_guru' => $id_guru,
                'mata_pelajaran' => $mapped_mapels[$i],
                'kelas' => $mapped_kelases[$i]
            ]);
        }

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

        $db = \Config\Database::connect();
        $mappings = $db->table('guru_mapel_kelas')
            ->where('id_guru', $id_guru)
            ->get()
            ->getResultArray();

        $data = [
            'title'      => 'Edit Guru',
            'guru'       => $guru,
            'mappings'   => $mappings,
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
            'nik'                => "required|numeric|min_length[10]|max_length[20]|is_unique[guru.nik,id_guru,{$id_guru}]",
            'nama_guru'          => 'required|min_length[2]|max_length[100]',
            'jenis_kelamin'      => 'required|in_list[L,P]',
            'status_kepegawaian' => 'required',
            'tanggal_masuk'      => 'required|valid_date[Y-m-d]'
        ];

        $messages = [
            'nik' => [
                'is_unique' => 'NIK sudah digunakan oleh guru lain.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status_kepegawaian = $this->request->getPost('status_kepegawaian');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');

        $db = \Config\Database::connect();

        $mapped_mapels = $this->request->getPost('mapped_mapel') ?: [];
        $mapped_kelases = $this->request->getPost('mapped_kelas') ?: [];

        $errors = [];
        if (empty($mapped_mapels) || empty($mapped_kelases) || count($mapped_mapels) !== count($mapped_kelases)) {
            $errors['mappings'] = 'Pemetaan mata pelajaran dan kelas harus diisi minimal satu.';
        } else {
            $seen = [];
            for ($i = 0; $i < count($mapped_mapels); $i++) {
                $mapel = $mapped_mapels[$i];
                $kelas = $mapped_kelases[$i];
                if (empty($mapel) || empty($kelas)) {
                    $errors['mappings'] = 'Mata pelajaran dan kelas tidak boleh kosong.';
                    break;
                }
                $key = $mapel . '-' . $kelas;
                if (in_array($key, $seen)) {
                    $errors['mappings'] = 'Pemetaan untuk ' . $mapel . ' di Kelas ' . $kelas . ' duplikat dalam form.';
                    break;
                }
                $seen[] = $key;

                $existing = $db->table('guru_mapel_kelas')
                    ->select('guru_mapel_kelas.*, guru.nama_guru')
                    ->join('guru', 'guru.id_guru = guru_mapel_kelas.id_guru')
                    ->where('guru_mapel_kelas.mata_pelajaran', $mapel)
                    ->where('guru_mapel_kelas.kelas', $kelas)
                    ->where('guru_mapel_kelas.id_guru !=', $id_guru)
                    ->get()
                    ->getRow();
                if ($existing) {
                    $errors['mappings'] = 'Mata pelajaran ' . $mapel . ' di Kelas ' . $kelas . ' sudah diajar oleh guru ' . $existing->nama_guru . '.';
                    break;
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $year2 = date('y', strtotime($tanggal_masuk));
        $statusMap = [
            'PNS' => '01',
            'Honorer' => '02',
            'P3K' => '03'
        ];
        $statusKey = strtoupper($status_kepegawaian);
        $statusCode = isset($statusMap[$statusKey]) ? $statusMap[$statusKey] : '00';
        $prefix = $year2 . $statusCode;

        $new_id_guru = $id_guru;
        $oldPrefix = substr((string)$id_guru, 0, 4);
        if ($prefix !== $oldPrefix) {
            // Generate new ID Guru because status or date changed
            $query = $db->query("SELECT id_guru FROM guru WHERE CAST(id_guru AS CHAR) LIKE '$prefix%' AND LENGTH(CAST(id_guru AS CHAR)) = 8 ORDER BY id_guru DESC LIMIT 1");
            $row = $query->getRow();
            if ($row) {
                $lastSeq = intval(substr($row->id_guru, 4));
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }
            $new_id_guru = intval($prefix . sprintf('%04d', $nextSeq));
        }

        $unique_mapels = array_unique($mapped_mapels);
        $unique_kelases = array_unique($mapped_kelases);
        $mata_pelajaran_str = implode(',', $unique_mapels);
        $kelas_diajar_str = implode(',', $unique_kelases);

        $db->transBegin();

        $updateData = [
            'nik'                => $this->request->getPost('nik'),
            'nama_guru'          => $this->request->getPost('nama_guru'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'mata_pelajaran'     => $mata_pelajaran_str,
            'kelas_diajar'       => $kelas_diajar_str,
            'status_kepegawaian' => $status_kepegawaian,
            'tanggal_masuk'      => $tanggal_masuk
        ];

        if ($new_id_guru !== $id_guru) {
            $updateData['id_guru'] = $new_id_guru;
        }

        // 1. Update guru
        $db->table('guru')->where('id_guru', $id_guru)->update($updateData);

        if ($new_id_guru !== $id_guru) {
            // 2. Update users
            $db->table('users')->where('ref_id', (string)$id_guru)->where('role', 'guru')->update(['ref_id' => (string)$new_id_guru]);

            // 3. Update nilai
            $db->table('nilai')->where('id_guru', $id_guru)->update(['id_guru' => $new_id_guru]);
        }

        // 4. Update mappings: delete old ones first (using old ID)
        $db->table('guru_mapel_kelas')->where('id_guru', $id_guru)->delete();

        // Insert new ones (using the possibly new ID)
        for ($i = 0; $i < count($mapped_mapels); $i++) {
            $db->table('guru_mapel_kelas')->insert([
                'id_guru' => $new_id_guru,
                'mata_pelajaran' => $mapped_mapels[$i],
                'kelas' => $mapped_kelases[$i]
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data guru.');
        }

        $db->transCommit();
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
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Data guru tidak ditemukan. Silakan login kembali.');
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
            'nik'                 => "required|numeric|min_length[10]|max_length[20]|is_unique[guru.nik,id_guru,{$id_guru}]",
            'jenis_kelamin'       => 'required|in_list[L,P]',
            'tempat_lahir'        => 'permit_empty|max_length[100]',
            'tanggal_lahir'       => 'permit_empty|valid_date[Y-m-d]',
            'alamat'              => 'permit_empty',
            'no_hp'               => 'permit_empty|max_length[20]',
            'email'               => 'permit_empty|valid_email|max_length[100]',
            'pendidikan_terakhir' => 'permit_empty|max_length[50]',
            'status_kepegawaian'  => 'permit_empty|max_length[50]',
            'tanggal_masuk'       => 'permit_empty|valid_date[Y-m-d]'
        ];

        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        $messages = [
            'nik' => [
                'is_unique' => 'NIK sudah digunakan oleh guru lain.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mata_pelajaran = $this->request->getPost('mata_pelajaran');
        $kelas_diajar = $this->request->getPost('kelas_diajar');
        $mata_pelajaran_str = is_array($mata_pelajaran) ? implode(',', $mata_pelajaran) : $mata_pelajaran;
        $kelas_diajar_str = is_array($kelas_diajar) ? implode(',', $kelas_diajar) : $kelas_diajar;

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
            'mata_pelajaran'      => $mata_pelajaran_str,
            'kelas_diajar'        => $kelas_diajar_str,
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
