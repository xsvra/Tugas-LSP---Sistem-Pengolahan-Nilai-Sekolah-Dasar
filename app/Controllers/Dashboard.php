<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\GuruModel;
use App\Models\NilaiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        if ($role === 'siswa') {
            return redirect()->to('/profil/siswa');
        } elseif ($role === 'guru') {
            return redirect()->to('/profil/guru');
        }

        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();
        $nilaiModel = new NilaiModel();

        $totalSiswa = $siswaModel->countAllResults();
        $totalGuru = $guruModel->countAllResults();
        
        $daftarNilai = $nilaiModel->getNilaiWithDetails();
        
        // Load structured helper
        helper('structured');
        $statistik = proses_laporan($daftarNilai);

        // 1. Hitung Lulus/Tidak Lulus berdasarkan Siswa (bukan record nilai)
        $siswaList = $siswaModel->findAll();
        $totalLulusSiswa = 0;
        $totalTidakLulusSiswa = 0;

        foreach ($siswaList as $s) {
            $siswaGrades = $nilaiModel->where('nis', $s['nis'])->findAll();
            $jumlahMapel = count($siswaGrades);
            
            if ($jumlahMapel > 0) {
                $totalNilai = array_sum(array_column($siswaGrades, 'nilai_akhir'));
                $rataRata = $totalNilai / $jumlahMapel;
                if ($rataRata >= 75) {
                    $totalLulusSiswa++;
                } else {
                    $totalTidakLulusSiswa++;
                }
            } else {
                $totalTidakLulusSiswa++;
            }
        }
        $persentaseKelulusanSiswa = $totalSiswa > 0 ? ($totalLulusSiswa / $totalSiswa) * 100 : 0.0;

        // 2. Hitung Statistik Per Mata Pelajaran
        $subjectStats = [];
        foreach ($daftarNilai as $n) {
            $subject = $n['mata_pelajaran'];
            if (!isset($subjectStats[$subject])) {
                $subjectStats[$subject] = [
                    'nama' => $subject,
                    'total_nilai' => 0.0,
                    'count' => 0,
                    'lulus' => 0,
                    'tidak_lulus' => 0
                ];
            }
            $subjectStats[$subject]['total_nilai'] += (float)$n['nilai_akhir'];
            $subjectStats[$subject]['count']++;
            if (strtolower($n['status_kelulusan']) === 'lulus') {
                $subjectStats[$subject]['lulus']++;
            } else {
                $subjectStats[$subject]['tidak_lulus']++;
            }
        }

        foreach ($subjectStats as &$sub) {
            $sub['rata_rata'] = $sub['count'] > 0 ? $sub['total_nilai'] / $sub['count'] : 0.0;
        }
        unset($sub);

        $data = [
            'title'                      => 'Dashboard',
            'total_siswa'                => $totalSiswa,
            'total_guru'                 => $totalGuru,
            'statistik'                  => $statistik,
            'total_lulus_siswa'          => $totalLulusSiswa,
            'total_tidak_lulus_siswa'    => $totalTidakLulusSiswa,
            'persentase_kelulusan_siswa' => $persentaseKelulusanSiswa,
            'recent_nilai'               => $daftarNilai, // Send all grades, layout will limit view
            'all_nilai'                  => $daftarNilai,
            'subject_stats'              => $subjectStats
        ];

        return view('dashboard', $data);
    }

    /**
     * Halaman dokumen rancangan sistem (Tugas 1)
     */
    public function dokumenRancangan()
    {
        $data = [
            'title' => 'Dokumen Rancangan Sistem'
        ];
        return view('dokumen_rancangan', $data);
    }
}
