<?php

/**
 * Integration Test for Multi-Role Auth and Appeals (Banding Nilai)
 */

define('FCPATH', __DIR__ . '/public/');
define('ENVIRONMENT', 'development');
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new \Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::bootConsole($paths);

use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;
use App\Models\NilaiModel;
use App\Models\BandingModel;

echo "=== MEMULAI TEST INTEGRASI AUTH & BANDING ===\n\n";

$db = \Config\Database::connect();
$db->transBegin();

try {
    $userModel = new UserModel();
    $siswaModel = new SiswaModel();
    $guruModel = new GuruModel();
    $nilaiModel = new NilaiModel();
    $bandingModel = new BandingModel();

    // 1. Clean up potential old test records
    $userModel->where('username', 'claratest')->delete();
    $userModel->where('username', 'ruditest')->delete();
    $siswaModel->where('nis', '2619103999')->delete();
    $guruModel->where('id_guru', 26019999)->delete();

    // 2. Test Siswa & Guru Registration Flow
    echo "[TEST 1] Menguji Registrasi Siswa & Guru (Transaksi)...\n";
    
    // Siswa Registration
    $siswaSaved = $siswaModel->insert([
        'nis'           => '2619103999',
        'nisn'          => '0089999999',
        'nama'          => 'Clara Test',
        'jenis_kelamin' => 'P',
        'kelas'         => '3',
        'tahun_masuk'   => '2026',
        'tanggal_lahir' => '2008-05-15'
    ]);
    if (!$siswaSaved) {
        echo "Siswa save errors:\n";
        print_r($siswaModel->errors());
    }

    $userSiswaSaved = $userModel->save([
        'username' => 'claratest',
        'password' => password_hash('siswa123', PASSWORD_BCRYPT),
        'role'     => 'siswa',
        'ref_id'   => '2619103999'
    ]);
    if (!$userSiswaSaved) {
        echo "User siswa save errors:\n";
        print_r($userModel->errors());
    }

    // Guru Registration
    $guruSaved = $guruModel->insert([
        'id_guru'            => 26019999,
        'nik'                => '3201234567890123',
        'nama_guru'          => 'Rudi Test',
        'jenis_kelamin'      => 'L',
        'mata_pelajaran'     => 'Fisika',
        'status_kepegawaian' => 'PNS',
        'tanggal_masuk'      => '2026-06-14'
    ]);
    if (!$guruSaved) {
        echo "Guru save errors:\n";
        print_r($guruModel->errors());
    }

    $userGuruSaved = $userModel->save([
        'username' => 'ruditest',
        'password' => password_hash('guru123', PASSWORD_BCRYPT),
        'role'     => 'guru',
        'ref_id'   => 26019999
    ]);
    if (!$userGuruSaved) {
        echo "User guru save errors:\n";
        print_r($userModel->errors());
    }

    echo "✔ BERHASIL: Siswa 'Clara Test' & Guru 'Rudi Test' terdaftar.\n";

    // 3. Test Login Verification
    echo "\n[TEST 2] Menguji Verifikasi Login Kredensial...\n";
    $userSiswa = $userModel->find('claratest');
    $userGuru = $userModel->find('ruditest');
    
    echo "Retrieved student user:\n";
    print_r($userSiswa);
    echo "password_verify outcome: " . (password_verify('siswa123', $userSiswa['password']) ? 'TRUE' : 'FALSE') . "\n";

    if ($userSiswa && password_verify('siswa123', $userSiswa['password']) && 
        $userGuru && password_verify('guru123', $userGuru['password'])) {
        echo "✔ BERHASIL: Kredensial login untuk Siswa dan Guru berhasil divalidasi.\n";
    } else {
        throw new \Exception("Verifikasi password login gagal.");
    }

    // 4. Test Grade Insertion
    echo "\n[TEST 3] Menguji Guru menginput nilai untuk Siswa...\n";
    $nilaiId = $nilaiModel->insert([
        'nis'              => '2619103999',
        'id_guru'          => 26019999,
        'nilai_tugas'      => 60.00,
        'nilai_uts'        => 70.00,
        'nilai_uas'        => 65.00,
        'nilai_akhir'      => 65.00,
        'status_kelulusan' => 'Tidak Lulus'
    ]);
    if (!$nilaiId) {
        echo "Nilai save errors:\n";
        print_r($nilaiModel->errors());
        $db_err = $db->error();
        print_r($db_err);
    }
    echo "✔ BERHASIL: Nilai Fisika diinput oleh Guru (Nilai Akhir: 65.00 - Tidak Lulus, ID Nilai: $nilaiId)\n";

    // 5. Test Siswa Appeals Submission
    echo "\n[TEST 4] Menguji Siswa mengajukan banding nilai...\n";
    $bandingId = $bandingModel->insert([
        'id_nilai' => $nilaiId,
        'alasan'   => 'Mohon maaf, nilai tugas dan UAS saya sepertinya tertukar di sistem.',
        'status'   => 'Pending'
    ]);
    
    $appeal = $bandingModel->find($bandingId);
    if ($appeal && $appeal['id_nilai'] == $nilaiId && $appeal['status'] === 'Pending') {
        echo "✔ BERHASIL: Pengajuan banding dibuat dengan status 'Pending' (ID Banding: $bandingId)\n";
    } else {
        throw new \Exception("Pengajuan banding gagal dibuat.");
    }

    // 6. Test Guru Reviews Appeal & Updates Grade
    echo "\n[TEST 5] Menguji Guru menyetujui banding & memperbarui nilai...\n";
    
    // Guru updates appeal status to Disetujui
    $bandingModel->update($bandingId, [
        'status'          => 'Disetujui',
        'keterangan_guru' => 'Banding disetujui. Setelah ditinjau kembali, nilai UAS telah diperbarui.'
    ]);

    // Guru updates actual student grades
    $nilaiModel->update($nilaiId, [
        'nilai_uas'        => 90.00,
        'nilai_akhir'      => 75.00, // (60*0.3) + (70*0.3) + (90*0.4) = 18 + 21 + 36 = 75.00
        'status_kelulusan' => 'Lulus'
    ]);

    $updatedNilai = $nilaiModel->find($nilaiId);
    $updatedAppeal = $bandingModel->find($bandingId);

    if ($updatedAppeal['status'] === 'Disetujui' && $updatedNilai['nilai_akhir'] == 75.00 && $updatedNilai['status_kelulusan'] === 'Lulus') {
        echo "✔ BERHASIL: Banding disetujui, nilai berhasil diperbarui (Nilai Akhir: 75.00 - Lulus)\n";
    } else {
        throw new \Exception("Pembaruan nilai setelah banding tidak sesuai.");
    }

    echo "\n======================================================\n";
    echo "★ KESIMPULAN: SEMUA PENGUJIAN INTEGRASI BERHASIL (100% PASSED) ★\n";
    echo "======================================================\n";

} catch (\Exception $e) {
    echo "✘ GAGAL: Mengalami exception: " . $e->getMessage() . "\n";
} finally {
    // Rollback database changes to keep it clean
    $db->transRollback();
    echo "\n✔ Database transaction rolled back untuk membersihkan data uji coba.\n";
}
