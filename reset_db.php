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
            'id_guru' => 20010001,
            'nik' => '1234567890123451',
            'nama_guru' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => 'Matematika,IPA',
            'kelas_diajar' => '5,6',
            'no_hp' => '081234567890',
            'email' => 'budi@sekolah.sch.id',
            'status_kepegawaian' => 'PNS',
            'tanggal_masuk' => '2020-07-15'
        ],
        [
            'id_guru' => 21020001,
            'nik' => '1234567890123452',
            'nama_guru' => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'mata_pelajaran' => 'Bahasa Indonesia,PKN',
            'kelas_diajar' => '4,5',
            'no_hp' => '081234567891',
            'email' => 'siti@sekolah.sch.id',
            'status_kepegawaian' => 'Honorer',
            'tanggal_masuk' => '2021-08-20'
        ],
        [
            'id_guru' => 19010001,
            'nik' => '1234567890123453',
            'nama_guru' => 'Rudi Hermawan',
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => 'IPA,PJOK',
            'kelas_diajar' => '4,5,6',
            'no_hp' => '081234567892',
            'email' => 'rudi@sekolah.sch.id',
            'status_kepegawaian' => 'PNS',
            'tanggal_masuk' => '2019-03-10'
        ]
    ];
    foreach ($gurus as $g) {
        $db->table('guru')->insert($g);
    }
    echo "✔ Guru data seeded.\n";

    // 1b. Insert Pemetaan Mapel & Kelas Guru
    $mappings = [
        // Budi
        ['id_guru' => 20010001, 'mata_pelajaran' => 'Matematika', 'kelas' => '5'],
        ['id_guru' => 20010001, 'mata_pelajaran' => 'Matematika', 'kelas' => '6'],
        ['id_guru' => 20010001, 'mata_pelajaran' => 'IPA', 'kelas' => '5'],
        ['id_guru' => 20010001, 'mata_pelajaran' => 'IPA', 'kelas' => '6'],
        // Siti
        ['id_guru' => 21020001, 'mata_pelajaran' => 'Bahasa Indonesia', 'kelas' => '4'],
        ['id_guru' => 21020001, 'mata_pelajaran' => 'Bahasa Indonesia', 'kelas' => '5'],
        ['id_guru' => 21020001, 'mata_pelajaran' => 'PKN', 'kelas' => '4'],
        ['id_guru' => 21020001, 'mata_pelajaran' => 'PKN', 'kelas' => '5'],
        // Rudi
        ['id_guru' => 19010001, 'mata_pelajaran' => 'PJOK', 'kelas' => '5'],
        ['id_guru' => 19010001, 'mata_pelajaran' => 'PJOK', 'kelas' => '6'],
        ['id_guru' => 19010001, 'mata_pelajaran' => 'IPA', 'kelas' => '4'],
    ];
    foreach ($mappings as $m) {
        $db->table('guru_mapel_kelas')->insert($m);
    }
    echo "✔ Guru mapel kelas mappings seeded.\n";

    // 2. Insert Siswa
    $siswas = [
        [
            'id_siswa' => 1,
            'nis' => '2019103001',
            'nisn' => '0081234567',
            'nama' => 'Ani Wijaya',
            'jenis_kelamin' => 'P',
            'kelas' => '6',
            'tahun_masuk' => '2020',
            'tanggal_lahir' => '2008-05-15',
            'nama_wali' => 'Hendra Wijaya',
            'no_hp_wali' => '082111111111',
            'status_siswa' => 'Aktif'
        ],
        [
            'id_siswa' => 2,
            'nis' => '2019103002',
            'nisn' => '0082345678',
            'nama' => 'Bambang Pamungkas',
            'jenis_kelamin' => 'L',
            'kelas' => '6',
            'tahun_masuk' => '2020',
            'tanggal_lahir' => '2008-11-20',
            'nama_wali' => 'Sutrisno',
            'no_hp_wali' => '082122222222',
            'status_siswa' => 'Aktif'
        ],
        [
            'id_siswa' => 3,
            'nis' => '2119103001',
            'nisn' => '0093456789',
            'nama' => 'Chandra Li',
            'jenis_kelamin' => 'L',
            'kelas' => '5',
            'tahun_masuk' => '2021',
            'tanggal_lahir' => '2009-02-10',
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
        ['username' => 'budi', 'password' => $password, 'role' => 'guru', 'ref_id' => '20010001'],
        ['username' => 'siti', 'password' => $password, 'role' => 'guru', 'ref_id' => '21020001'],
        ['username' => 'rudi', 'password' => $password, 'role' => 'guru', 'ref_id' => '19010001'],
        // Siswa
        ['username' => 'ani', 'password' => $password, 'role' => 'siswa', 'ref_id' => '2019103001'],
        ['username' => 'bambang', 'password' => $password, 'role' => 'siswa', 'ref_id' => '2019103002'],
        ['username' => 'chandra', 'password' => $password, 'role' => 'siswa', 'ref_id' => '2119103001'],
    ];
    foreach ($users as $u) {
        $db->table('users')->insert($u);
    }
    echo "✔ User accounts seeded (Password: sekolah123).\n";

    // 4. Insert Nilai (satu record = satu siswa + satu mata pelajaran)
    // Guru Budi mengajar: Matematika, IPA (Kelas 5, 6)
    // Guru Siti mengajar: Bahasa Indonesia, PKN (Kelas 4, 5)
    // Guru Rudi mengajar: IPA, PJOK (Kelas 5, 6)
    $nilais = [
        // === Ani Wijaya (2019103001, Kelas 6) ===
        // Guru Budi - Matematika
        ['nis' => '2019103001', 'id_guru' => 20010001, 'mata_pelajaran' => 'Matematika', 'nilai_tugas' => 80.00, 'nilai_uts' => 75.00, 'nilai_uas' => 85.00, 'nilai_akhir' => 80.50, 'status_kelulusan' => 'Lulus'],
        // Guru Budi - IPA
        ['nis' => '2019103001', 'id_guru' => 20010001, 'mata_pelajaran' => 'IPA', 'nilai_tugas' => 70.00, 'nilai_uts' => 72.00, 'nilai_uas' => 78.00, 'nilai_akhir' => 73.80, 'status_kelulusan' => 'Tidak Lulus'],
        // Guru Siti - Bahasa Indonesia (Siti mengajar kelas 4,5 — tapi Ani kelas 6, skip)
        // Guru Rudi - IPA (overlap mapel IPA — dihandle oleh Budi, skip untuk Rudi)
        // Guru Rudi - PJOK
        ['nis' => '2019103001', 'id_guru' => 19010001, 'mata_pelajaran' => 'PJOK', 'nilai_tugas' => 70.00, 'nilai_uts' => 70.00, 'nilai_uas' => 75.00, 'nilai_akhir' => 72.00, 'status_kelulusan' => 'Tidak Lulus'],

        // === Bambang Pamungkas (2019103002, Kelas 6) ===
        // Guru Budi - Matematika
        ['nis' => '2019103002', 'id_guru' => 20010001, 'mata_pelajaran' => 'Matematika', 'nilai_tugas' => 60.00, 'nilai_uts' => 65.00, 'nilai_uas' => 70.00, 'nilai_akhir' => 65.50, 'status_kelulusan' => 'Tidak Lulus'],
        // Guru Budi - IPA
        ['nis' => '2019103002', 'id_guru' => 20010001, 'mata_pelajaran' => 'IPA', 'nilai_tugas' => 75.00, 'nilai_uts' => 80.00, 'nilai_uas' => 75.00, 'nilai_akhir' => 76.50, 'status_kelulusan' => 'Lulus'],
        // Guru Rudi - PJOK
        ['nis' => '2019103002', 'id_guru' => 19010001, 'mata_pelajaran' => 'PJOK', 'nilai_tugas' => 80.00, 'nilai_uts' => 80.00, 'nilai_uas' => 80.00, 'nilai_akhir' => 80.00, 'status_kelulusan' => 'Lulus'],

        // === Chandra Li (2119103001, Kelas 5) ===
        // Guru Budi - Matematika
        ['nis' => '2119103001', 'id_guru' => 20010001, 'mata_pelajaran' => 'Matematika', 'nilai_tugas' => 85.00, 'nilai_uts' => 90.00, 'nilai_uas' => 95.00, 'nilai_akhir' => 90.50, 'status_kelulusan' => 'Lulus'],
        // Guru Budi - IPA
        ['nis' => '2119103001', 'id_guru' => 20010001, 'mata_pelajaran' => 'IPA', 'nilai_tugas' => 88.00, 'nilai_uts' => 85.00, 'nilai_uas' => 87.00, 'nilai_akhir' => 86.70, 'status_kelulusan' => 'Lulus'],
        // Guru Siti - Bahasa Indonesia (Siti mengajar kelas 4,5 — Chandra kelas 5, cocok)
        ['nis' => '2119103001', 'id_guru' => 21020001, 'mata_pelajaran' => 'Bahasa Indonesia', 'nilai_tugas' => 82.00, 'nilai_uts' => 80.00, 'nilai_uas' => 85.00, 'nilai_akhir' => 82.60, 'status_kelulusan' => 'Lulus'],
        // Guru Siti - PKN
        ['nis' => '2119103001', 'id_guru' => 21020001, 'mata_pelajaran' => 'PKN', 'nilai_tugas' => 78.00, 'nilai_uts' => 76.00, 'nilai_uas' => 80.00, 'nilai_akhir' => 78.20, 'status_kelulusan' => 'Lulus'],
        // Guru Rudi - PJOK
        ['nis' => '2119103001', 'id_guru' => 19010001, 'mata_pelajaran' => 'PJOK', 'nilai_tugas' => 92.00, 'nilai_uts' => 90.00, 'nilai_uas' => 94.00, 'nilai_akhir' => 92.20, 'status_kelulusan' => 'Lulus'],
    ];
    foreach ($nilais as $n) {
        $db->table('nilai')->insert($n);
    }
    echo "✔ Grades (nilai) seeded.\n";

    echo "\n=== ALL DATABASE SEEDING COMPLETED SUCCESSFULLY ===\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
