<?php

if (!function_exists('validasi_nilai')) {
    /**
     * Memastikan nilai berada di rentang 0-100.
     * 
     * @param float|int $nilai
     * @return bool
     */
    function validasi_nilai($nilai) {
        return is_numeric($nilai) && $nilai >= 0 && $nilai <= 100;
    }
}

if (!function_exists('hitung_nilai_akhir')) {
    /**
     * Menghitung nilai akhir dengan bobot (30% Tugas, 30% UTS, 40% UAS).
     * 
     * @param float $tugas
     * @param float $uts
     * @param float $uas
     * @return float
     */
    function hitung_nilai_akhir($tugas, $uts, $uas) {
        return ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
    }
}

if (!function_exists('tentukan_status_kelulusan')) {
    /**
     * Menentukan kelulusan berdasarkan KKM 75.
     * 
     * @param float $nilai_akhir
     * @return string
     */
    function tentukan_status_kelulusan($nilai_akhir) {
        return $nilai_akhir >= 75 ? 'Lulus' : 'Tidak Lulus';
    }
}

if (!function_exists('proses_laporan')) {
    /**
     * Menghitung ringkasan data statistik (nilai tertinggi, terendah, rata-rata, persentase kelulusan).
     * Dapat menangani array assosiatif (hasil query db) maupun array objek (domain OOP).
     * 
     * @param array $daftar_nilai
     * @return array
     */
    function proses_laporan($daftar_nilai) {
        if (empty($daftar_nilai)) {
            return [
                'tertinggi' => 0.00,
                'terendah' => 0.00,
                'rata_rata' => 0.00,
                'persentase_kelulusan' => 0.00,
                'total_siswa' => 0,
                'total_lulus' => 0,
                'total_tidak_lulus' => 0
            ];
        }

        $total_siswa = count($daftar_nilai);
        $total_lulus = 0;
        $nilai_akhir_list = [];

        foreach ($daftar_nilai as $nilai) {
            $nilai_akhir = 0.0;
            $status = 'Tidak Lulus';

            if (is_array($nilai)) {
                $nilai_akhir = isset($nilai['nilai_akhir']) ? (float)$nilai['nilai_akhir'] : 0.0;
                $status = isset($nilai['status_kelulusan']) ? $nilai['status_kelulusan'] : 'Tidak Lulus';
            } elseif (is_object($nilai)) {
                if (method_exists($nilai, 'getNilaiAkhir')) {
                    $nilai_akhir = (float)$nilai->getNilaiAkhir();
                    $status = method_exists($nilai, 'getStatusKelulusan') ? $nilai->getStatusKelulusan() : 'Tidak Lulus';
                } else {
                    $nilai_akhir = isset($nilai->nilai_akhir) ? (float)$nilai->nilai_akhir : 0.0;
                    $status = isset($nilai->status_kelulusan) ? $nilai->status_kelulusan : 'Tidak Lulus';
                }
            }

            $nilai_akhir_list[] = $nilai_akhir;
            if (strtolower($status) === 'lulus') {
                $total_lulus++;
            }
        }

        $tertinggi = !empty($nilai_akhir_list) ? max($nilai_akhir_list) : 0.0;
        $terendah = !empty($nilai_akhir_list) ? min($nilai_akhir_list) : 0.0;
        $rata_rata = !empty($nilai_akhir_list) ? array_sum($nilai_akhir_list) / count($nilai_akhir_list) : 0.0;
        $persentase_kelulusan = $total_siswa > 0 ? ($total_lulus / $total_siswa) * 100 : 0.0;

        return [
            'tertinggi' => round($tertinggi, 2),
            'terendah' => round($terendah, 2),
            'rata_rata' => round($rata_rata, 2),
            'persentase_kelulusan' => round($persentase_kelulusan, 2),
            'total_siswa' => $total_siswa,
            'total_lulus' => $total_lulus,
            'total_tidak_lulus' => $total_siswa - $total_lulus
        ];
    }
}
