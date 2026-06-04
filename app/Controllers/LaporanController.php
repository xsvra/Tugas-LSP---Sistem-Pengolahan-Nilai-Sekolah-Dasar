<?php

namespace App\Controllers;

use App\Models\NilaiModel;
use App\Models\SiswaModel;
use App\Models\GuruModel;

class LaporanController extends BaseController
{
    public function index()
    {
        $nilaiModel = new NilaiModel();
        $siswaModel = new SiswaModel();

        $role = session()->get('role');
        $idGuru = session()->get('ref_id');

        if ($role === 'guru') {
            $daftarNilai = $nilaiModel->select('nilai.*, siswa.nama as nama_siswa, siswa.kelas, guru.nama_guru, guru.mata_pelajaran')
                ->join('siswa', 'siswa.nis = nilai.nis')
                ->join('guru', 'guru.id_guru = nilai.id_guru')
                ->where('nilai.id_guru', $idGuru)
                ->orderBy('nilai.created_at', 'DESC')
                ->findAll();
        } else {
            $daftarNilai = $nilaiModel->getNilaiWithDetails();
        }
        
        // Load structured helper
        helper('structured');
        $statistik = proses_laporan($daftarNilai);

        // Rekapitulasi nilai per siswa
        $siswaList = $siswaModel->findAll();
        $rekapSiswa = [];

        foreach ($siswaList as $siswa) {
            if ($role === 'guru') {
                $siswaGrades = $nilaiModel->where('nis', $siswa['nis'])->where('id_guru', $idGuru)->findAll();
            } else {
                $siswaGrades = $nilaiModel->where('nis', $siswa['nis'])->findAll();
            }
            $jumlahMapel = count($siswaGrades);
            
            $rataRata = 0.0;
            $status = 'Tidak Lulus';

            if ($jumlahMapel > 0) {
                $totalNilai = array_sum(array_column($siswaGrades, 'nilai_akhir'));
                $rataRata = $totalNilai / $jumlahMapel;
                $status = tentukan_status_kelulusan($rataRata);
            }

            // Only show students in rekap if they are active or have grades for this teacher
            if ($siswa['status_siswa'] === 'Aktif' || $jumlahMapel > 0) {
                $rekapSiswa[] = [
                    'nis'          => $siswa['nis'],
                    'nama'         => $siswa['nama'],
                    'kelas'        => $siswa['kelas'],
                    'jumlah_mapel' => $jumlahMapel,
                    'rata_rata'    => round($rataRata, 2),
                    'status'       => $status
                ];
            }
        }

        $data = [
            'title'       => 'Laporan & Rekap Nilai',
            'statistik'   => $statistik,
            'rekap_siswa' => $rekapSiswa
        ];

        return view('laporan/index', $data);
    }

    /**
     * Halaman cetak rapor untuk siswa tertentu
     * 
     * @param string $nis
     */
    public function rapor($nis)
    {
        // Pengecekan Keamanan: Guru tidak diperbolehkan melihat/mencetak rapor siswa
        if (session()->get('role') === 'guru') {
            return redirect()->to('/laporan')->with('error', 'Akses ditolak. Guru tidak diperbolehkan melihat atau mencetak rapor siswa.');
        }

        // Pengecekan Keamanan: Siswa hanya boleh melihat rapor miliknya sendiri
        if (session()->get('role') === 'siswa' && session()->get('ref_id') !== $nis) {
            return redirect()->to('/')->with('error', 'Akses ditolak. Anda hanya diperbolehkan melihat rapor Anda sendiri.');
        }

        $siswaModel = new SiswaModel();
        $nilaiModel = new NilaiModel();

        $siswa = $siswaModel->where('nis', $nis)->first();
        if (!$siswa) {
            return redirect()->to('/laporan')->with('error', 'Siswa tidak ditemukan.');
        }

        // Ambil semua nilai untuk siswa ini
        $nilai = $nilaiModel->select('nilai.*, guru.nama_guru, guru.mata_pelajaran')
            ->join('guru', 'guru.id_guru = nilai.id_guru')
            ->where('nilai.nis', $nis)
            ->findAll();

        helper('structured');
        $statistik = proses_laporan($nilai);

        $data = [
            'title'     => 'Rapor Siswa - ' . $siswa['nama'],
            'siswa'     => $siswa,
            'nilai'     => $nilai,
            'statistik' => $statistik
        ];

        return view('laporan/rapor', $data);
    }
}
