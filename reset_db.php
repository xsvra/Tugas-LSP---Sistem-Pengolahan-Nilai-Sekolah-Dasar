<?php
define('FCPATH', __DIR__ . '/public/');
define('ENVIRONMENT', 'development');
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new \Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::bootConsole($paths);

$db = \Config\Database::connect();

try {
    echo "=== RESETTING DATABASE SCHEMA ===\n";
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    $mysqli = $db->connID;
    if ($mysqli instanceof \mysqli) {
        if ($mysqli->multi_query($sql)) {
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
            echo "✔ Database schema reset successfully.\n";
        } else {
            throw new \Exception("Multi-query failed: " . $mysqli->error);
        }
    } else {
        // Fallback split execution
        $queries = explode(';', $sql);
        foreach ($queries as $q) {
            $q = trim($q);
            if (!empty($q)) {
                $db->query($q);
            }
        }
        echo "✔ Database schema reset successfully (via fallback split).\n";
    }

    echo "\n=== INSERTING SEED DATA ===\n";

    // 1. Insert Guru
    $gurus = [
        [
            'id_guru' => 1,
            'nik' => '1234567890123451',
            'nama_guru' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => 'Matematika',
            'no_hp' => '081234567890',
            'email' => 'budi@sekolah.sch.id',
            'status_kepegawaian' => 'PNS'
        ],
        [
            'id_guru' => 2,
            'nik' => '1234567890123452',
            'nama_guru' => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'mata_pelajaran' => 'Bahasa Indonesia',
            'no_hp' => '081234567891',
            'email' => 'siti@sekolah.sch.id',
            'status_kepegawaian' => 'Honorer'
        ],
        [
            'id_guru' => 3,
            'nik' => '1234567890123453',
            'nama_guru' => 'Rudi Hermawan',
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => 'IPA',
            'no_hp' => '081234567892',
            'email' => 'rudi@sekolah.sch.id',
            'status_kepegawaian' => 'PNS'
        ]
    ];
    foreach ($gurus as $g) {
        $db->table('guru')->insert($g);
    }
    echo "✔ Guru data seeded.\n";

    // 2. Insert Siswa
    $siswas = [
        [
            'id_siswa' => 1,
            'nis' => '101',
            'nisn' => '0012345671',
            'nama' => 'Ani Wijaya',
            'jenis_kelamin' => 'P',
            'kelas' => '6',
            'tahun_masuk' => '2020',
            'nama_wali' => 'Hendra Wijaya',
            'no_hp_wali' => '082111111111',
            'status_siswa' => 'Aktif'
        ],
        [
            'id_siswa' => 2,
            'nis' => '102',
            'nisn' => '0012345672',
            'nama' => 'Bambang Pamungkas',
            'jenis_kelamin' => 'L',
            'kelas' => '6',
            'tahun_masuk' => '2020',
            'nama_wali' => 'Sutrisno',
            'no_hp_wali' => '082122222222',
            'status_siswa' => 'Aktif'
        ],
        [
            'id_siswa' => 3,
            'nis' => '103',
            'nisn' => '0012345673',
            'nama' => 'Chandra Li',
            'jenis_kelamin' => 'L',
            'kelas' => '5',
            'tahun_masuk' => '2021',
            'nama_wali' => 'Joko Li',
            'no_hp_wali' => '082133333333',
            'status_siswa' => 'Aktif'
        ]
    ];
    foreach ($siswas as $s) {
        $db->table('siswa')->insert($s);
    }
    echo "✔ Siswa data seeded.\n";

    // 3. Insert Users
    $password = password_hash('sekolah123', PASSWORD_BCRYPT);
    $users = [
        // Guru
        ['username' => 'budi', 'password' => $password, 'role' => 'guru', 'ref_id' => '1'],
        ['username' => 'siti', 'password' => $password, 'role' => 'guru', 'ref_id' => '2'],
        ['username' => 'rudi', 'password' => $password, 'role' => 'guru', 'ref_id' => '3'],
        // Siswa
        ['username' => 'ani', 'password' => $password, 'role' => 'siswa', 'ref_id' => '101'],
        ['username' => 'bambang', 'password' => $password, 'role' => 'siswa', 'ref_id' => '102'],
        ['username' => 'chandra', 'password' => $password, 'role' => 'siswa', 'ref_id' => '103'],
    ];
    foreach ($users as $u) {
        $db->table('users')->insert($u);
    }
    echo "✔ User accounts seeded (Password: sekolah123).\n";

    // 4. Insert Nilai
    $nilais = [
        // Ani Wijaya (101)
        ['nis' => '101', 'id_guru' => 1, 'nilai_tugas' => 80.00, 'nilai_uts' => 75.00, 'nilai_uas' => 85.00, 'nilai_akhir' => 80.50, 'status_kelulusan' => 'Lulus'],
        ['nis' => '101', 'id_guru' => 2, 'nilai_tugas' => 90.00, 'nilai_uts' => 85.00, 'nilai_uas' => 80.00, 'nilai_akhir' => 84.50, 'status_kelulusan' => 'Lulus'],
        ['nis' => '101', 'id_guru' => 3, 'nilai_tugas' => 70.00, 'nilai_uts' => 70.00, 'nilai_uas' => 75.00, 'nilai_akhir' => 72.00, 'status_kelulusan' => 'Tidak Lulus'],

        // Bambang Pamungkas (102)
        ['nis' => '102', 'id_guru' => 1, 'nilai_tugas' => 60.00, 'nilai_uts' => 65.00, 'nilai_uas' => 70.00, 'nilai_akhir' => 65.50, 'status_kelulusan' => 'Tidak Lulus'],
        ['nis' => '102', 'id_guru' => 2, 'nilai_tugas' => 75.00, 'nilai_uts' => 80.00, 'nilai_uas' => 75.00, 'nilai_akhir' => 76.50, 'status_kelulusan' => 'Lulus'],
        ['nis' => '102', 'id_guru' => 3, 'nilai_tugas' => 80.00, 'nilai_uts' => 80.00, 'nilai_uas' => 80.00, 'nilai_akhir' => 80.00, 'status_kelulusan' => 'Lulus'],

        // Chandra Li (103)
        ['nis' => '103', 'id_guru' => 1, 'nilai_tugas' => 85.00, 'nilai_uts' => 90.00, 'nilai_uas' => 95.00, 'nilai_akhir' => 90.50, 'status_kelulusan' => 'Lulus'],
        ['nis' => '103', 'id_guru' => 2, 'nilai_tugas' => 88.00, 'nilai_uts' => 85.00, 'nilai_uas' => 87.00, 'nilai_akhir' => 86.70, 'status_kelulusan' => 'Lulus'],
        ['nis' => '103', 'id_guru' => 3, 'nilai_tugas' => 92.00, 'nilai_uts' => 90.00, 'nilai_uas' => 94.00, 'nilai_akhir' => 92.20, 'status_kelulusan' => 'Lulus'],
    ];
    foreach ($nilais as $n) {
        $db->table('nilai')->insert($n);
    }
    echo "✔ Grades (nilai) seeded.\n";

    echo "\n=== ALL DATABASE SEEDING COMPLETED SUCCESSFULLY ===\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
