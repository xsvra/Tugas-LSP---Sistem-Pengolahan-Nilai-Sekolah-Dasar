<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }
        return view('auth/login', [
            'title' => 'Login - Sistem Nilai'
        ]);
    }

    public function loginProcess()
    {
        $userModel = new UserModel();
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->find($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau Password salah.');
        }

        // Fetch profile photo & name
        $foto = null;
        $nama_lengkap = null;
        if ($user['role'] === 'siswa') {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->where('nis', $user['ref_id'])->first();
            $foto = $siswa ? $siswa['foto'] : null;
            $nama_lengkap = $siswa ? $siswa['nama'] : null;
        } elseif ($user['role'] === 'guru') {
            $guruModel = new GuruModel();
            $guru = $guruModel->find($user['ref_id']);
            $foto = $guru ? $guru['foto'] : null;
            $nama_lengkap = $guru ? $guru['nama_guru'] : null;
        } else {
            $nama_lengkap = 'Administrator';
        }

        // Simpan data user ke dalam session
        session()->set([
            'username'     => $user['username'],
            'nama_lengkap' => $nama_lengkap,
            'role'         => $user['role'],
            'ref_id'       => $user['ref_id'],
            'foto'         => $foto,
            'logged_in'    => true
        ]);

        return redirect()->to('/')->with('success', 'Selamat datang kembali, ' . ($nama_lengkap ?: $user['username']) . '!');
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }
        return view('auth/register', [
            'title' => 'Registrasi Akun Baru'
        ]);
    }

    public function registerProcess()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $siswaModel = new SiswaModel();

        // Validasi dasar
        $rules = [
            'username'           => 'required|alpha_numeric_space|min_length[3]|max_length[50]|is_unique[users.username]',
            'password'           => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        // Gunakan Database Transaction untuk memastikan integritas data
        $db->transBegin();

        $query = $db->query("SELECT COALESCE(MAX(CAST(nis AS UNSIGNED)), 100) + 1 AS next_nis FROM siswa");
        $nextNis = (string)$query->getRow()->next_nis;

        // Simpan data siswa baru secara otomatis (nama default = username)
        $siswaModel->save([
            'nis'           => $nextNis,
            'nisn'          => '0000000000',
            'nama'          => $username,
            'kelas'         => '1',
            'jenis_kelamin' => 'L',
            'status_siswa'  => 'Aktif'
        ]);

        $userModel->save([
            'username' => $username,
            'password' => $password,
            'role'     => 'siswa',
            'ref_id'   => $nextNis
        ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Registrasi gagal. Terjadi kesalahan pada database.');
        }

        $db->transCommit();
        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login menggunakan akun Anda.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
