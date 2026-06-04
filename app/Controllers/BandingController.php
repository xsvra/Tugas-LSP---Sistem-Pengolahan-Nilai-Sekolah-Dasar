<?php

namespace App\Controllers;

use App\Models\NilaiModel;
use App\Models\BandingModel;

class BandingController extends BaseController
{
    /**
     * Menampilkan form pengajuan banding bagi siswa
     */
    public function ajukan($id_nilai)
    {
        $nilaiModel = new NilaiModel();
        $nilai = $nilaiModel->getNilaiByIdWithDetails($id_nilai);

        if (!$nilai) {
            return redirect()->to('/laporan')->with('error', 'Data nilai tidak ditemukan.');
        }

        // Pastikan siswa hanya bisa mengajukan banding untuk nilainya sendiri
        if (session()->get('ref_id') !== $nilai['nis']) {
            return redirect()->to('/laporan')->with('error', 'Anda tidak memiliki hak akses untuk data ini.');
        }

        $data = [
            'title' => 'Ajukan Banding Nilai',
            'nilai' => $nilai
        ];

        return view('banding/ajukan', $data);
    }

    /**
     * Memproses pengajuan banding nilai dari siswa
     */
    public function ajukanProcess($id_nilai)
    {
        $nilaiModel = new NilaiModel();
        $bandingModel = new BandingModel();

        $nilai = $nilaiModel->find($id_nilai);
        if (!$nilai) {
            return redirect()->to('/laporan')->with('error', 'Data nilai tidak ditemukan.');
        }

        if (session()->get('ref_id') !== $nilai['nis']) {
            return redirect()->to('/laporan')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'alasan' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bandingModel->save([
            'id_nilai'         => $id_nilai,
            'alasan'           => $this->request->getPost('alasan'),
            'status'           => 'Pending',
            'nilai_tugas_asal' => $nilai['nilai_tugas'],
            'nilai_uts_asal'   => $nilai['nilai_uts'],
            'nilai_uas_asal'   => $nilai['nilai_uas'],
            'nilai_akhir_asal' => $nilai['nilai_akhir']
        ]);

        return redirect()->to('/banding/riwayat')->with('success', 'Pengajuan banding berhasil dikirim.');
    }

    /**
     * Menampilkan riwayat banding nilai siswa yang sedang login
     */
    public function riwayat()
    {
        $bandingModel = new BandingModel();
        $nis = session()->get('ref_id');

        $data = [
            'title'   => 'Riwayat Banding Nilai',
            'riwayat' => $bandingModel->getAppealsBySiswa($nis)
        ];

        return view('banding/riwayat', $data);
    }

    /**
     * Menampilkan daftar semua banding nilai untuk ditinjau oleh Guru
     */
    public function tinjau()
    {
        $bandingModel = new BandingModel();
        $idGuru = session()->get('ref_id');
        
        $data = [
            'title'   => 'Tinjau Banding Nilai',
            'appeals' => $bandingModel->getAppealsWithDetails($idGuru)
        ];

        return view('banding/tinjau', $data);
    }

    /**
     * Guru memproses persetujuan/penolakan banding nilai
     */
    public function tinjauUpdate($id)
    {
        $bandingModel = new BandingModel();
        $idGuru = session()->get('ref_id');

        // Check if appeal exists and belongs to this teacher
        $appeal = $bandingModel->select('banding_nilai.*, nilai.id_guru')
            ->join('nilai', 'nilai.id = banding_nilai.id_nilai')
            ->where('banding_nilai.id', $id)
            ->first();

        if (!$appeal || $appeal['id_guru'] !== $idGuru) {
            return redirect()->to('/banding/tinjau')->with('error', 'Data banding tidak ditemukan atau akses ditolak.');
        }

        $rules = [
            'status'          => 'required|in_list[Disetujui,Ditolak]',
            'keterangan_guru' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bandingModel->update($id, [
            'status'          => $this->request->getPost('status'),
            'keterangan_guru' => $this->request->getPost('keterangan_guru')
        ]);

        return redirect()->to('/banding/tinjau')->with('success', 'Status pengajuan banding berhasil diperbarui.');
    }
}
