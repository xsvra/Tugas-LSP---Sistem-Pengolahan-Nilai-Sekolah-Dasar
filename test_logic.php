<?php

/**
 * CLI Test Script to Verify structured helpers and OOP Logic
 */

// Manually require the files for CLI execution (bypassing CI4 autoloader for a direct unit test)
require_once __DIR__ . '/app/Helpers/structured_helper.php';
require_once __DIR__ . '/app/OOP/Siswa.php';
require_once __DIR__ . '/app/OOP/Guru.php';
require_once __DIR__ . '/app/OOP/Nilai.php';

use App\OOP\Siswa;
use App\OOP\Guru;
use App\OOP\Nilai;

$allPassed = true;

echo "=== MEMULAI PENGUJIAN LOGIKA SISTEM ===\n\n";

// 1. Uji Helper Terstruktur: hitung_nilai_akhir
echo "[TEST 1] Menguji Helper hitung_nilai_akhir...\n";
$tugas = 80.0;
$uts = 75.0;
$uas = 85.0;
$expectedNilaiAkhir = (80 * 0.3) + (75 * 0.3) + (85 * 0.4); // 24 + 22.5 + 34 = 80.5
$actualNilaiAkhir = hitung_nilai_akhir($tugas, $uts, $uas);

if (abs($actualNilaiAkhir - $expectedNilaiAkhir) < 0.0001) {
    echo "✔ BERHASIL: Nilai Akhir terhitung $actualNilaiAkhir (Ekspetasi: $expectedNilaiAkhir)\n";
} else {
    echo "✘ GAGAL: Nilai Akhir terhitung $actualNilaiAkhir (Ekspetasi: $expectedNilaiAkhir)\n";
    $allPassed = false;
}

// 2. Uji Helper Terstruktur: tentukan_status_kelulusan
echo "\n[TEST 2] Menguji Helper tentukan_status_kelulusan...\n";
$kkm = 75.0;
$statusLulus = tentukan_status_kelulusan($actualNilaiAkhir);
$statusGagal = tentukan_status_kelulusan(74.9);

if ($statusLulus === 'Lulus' && $statusGagal === 'Tidak Lulus') {
    echo "✔ BERHASIL: Nilai $actualNilaiAkhir -> $statusLulus, Nilai 74.9 -> $statusGagal\n";
} else {
    echo "✘ GAGAL: Penentuan status kelulusan bermasalah\n";
    $allPassed = false;
}

// 3. Uji Helper Terstruktur: validasi_nilai
echo "\n[TEST 3] Menguji Helper validasi_nilai...\n";
if (validasi_nilai(100) && validasi_nilai(0) && validasi_nilai(50.5) && !validasi_nilai(-1) && !validasi_nilai(101)) {
    echo "✔ BERHASIL: Validasi nilai berada di rentang 0-100 berfungsi dengan benar\n";
} else {
    echo "✘ GAGAL: Validasi nilai bermasalah\n";
    $allPassed = false;
}

// 4. Uji Integrasi OOP: Siswa, Guru, Nilai
echo "\n[TEST 4] Menguji Instansiasi dan Kalkulasi Otomatis via Kelas OOP...\n";
try {
    $siswa = new Siswa("101", "Ani Wijaya", "X-IPA");
    $guru = new Guru("G01", "Budi Santoso", "Matematika");
    
    $nilai = new Nilai(
        1,
        $siswa,
        $guru,
        $tugas,
        $uts,
        $uas
    );

    if ($nilai->getNilaiAkhir() === $expectedNilaiAkhir && $nilai->getStatusKelulusan() === 'Lulus') {
        echo "✔ BERHASIL: Objek Nilai berhasil dibuat. Nilai Akhir: " . $nilai->getNilaiAkhir() . ", Status: " . $nilai->getStatusKelulusan() . "\n";
        
        // Test array conversion
        $arr = $nilai->toArray();
        if ($arr['nis'] === '101' && $arr['id_guru'] === 'G01' && $arr['nilai_akhir'] == 80.5) {
            echo "✔ BERHASIL: Konversi toArray() sesuai spesifikasi database\n";
        } else {
            echo "✘ GAGAL: Konversi toArray() tidak sesuai\n";
            $allPassed = false;
        }
    } else {
        echo "✘ GAGAL: Perhitungan di dalam objek Nilai tidak sesuai\n";
        $allPassed = false;
    }
} catch (\Exception $e) {
    echo "✘ GAGAL: Mengalami exception: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 5. Uji Helper Terstruktur: proses_laporan
echo "\n[TEST 5] Menguji Helper proses_laporan...\n";
$daftarNilai = [
    [
        'nilai_akhir' => 80.5,
        'status_kelulusan' => 'Lulus'
    ],
    [
        'nilai_akhir' => 70.0,
        'status_kelulusan' => 'Tidak Lulus'
    ],
    [
        'nilai_akhir' => 90.0,
        'status_kelulusan' => 'Lulus'
    ]
];

$rekap = proses_laporan($daftarNilai);
$expectedAvg = (80.5 + 70.0 + 90.0) / 3; // 80.17
$expectedPassRate = (2 / 3) * 100; // 66.67%

if ($rekap['tertinggi'] == 90.0 && $rekap['terendah'] == 70.0 && abs($rekap['rata_rata'] - $expectedAvg) < 0.01 && abs($rekap['persentase_kelulusan'] - $expectedPassRate) < 0.01) {
    echo "✔ BERHASIL: Rekapitulasi laporan statistik sesuai (Rata-rata: " . $rekap['rata_rata'] . ", Kelulusan: " . $rekap['persentase_kelulusan'] . "%)\n";
} else {
    echo "✘ GAGAL: Rekapitulasi laporan tidak sesuai\n";
    $allPassed = false;
}

echo "\n========================================\n";
if ($allPassed) {
    echo "★ KESIMPULAN: SEMUA PENGUJIAN BERHASIL (100% PASSED) ★\n";
} else {
    echo "★ KESIMPULAN: ADA PENGUJIAN YANG GAGAL ★\n";
}
echo "========================================\n";
