<?php

namespace App\Models;

use CodeIgniter\Model;

class BandingModel extends Model
{
    protected $table            = 'banding_nilai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_nilai', 'alasan', 'status', 'keterangan_guru',
        'nilai_tugas_asal', 'nilai_uts_asal', 'nilai_uas_asal', 'nilai_akhir_asal'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_nilai' => 'required|integer',
        'alasan'   => 'required|min_length[5]',
        'status'   => 'required|in_list[Pending,Disetujui,Ditolak]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Mendapatkan semua daftar banding nilai dengan informasi lengkap siswa, guru, mapel, dan nilai asal.
     * 
     * @return array
     */
    public function getAppealsWithDetails($idGuru = null)
    {
        $query = $this->select('banding_nilai.*, banding_nilai.nilai_tugas_asal as nilai_tugas, banding_nilai.nilai_uts_asal as nilai_uts, banding_nilai.nilai_uas_asal as nilai_uas, banding_nilai.nilai_akhir_asal as nilai_akhir, siswa.nama as nama_siswa, siswa.nis, siswa.kelas, guru.nama_guru, guru.mata_pelajaran')
            ->join('nilai', 'nilai.id = banding_nilai.id_nilai')
            ->join('siswa', 'siswa.nis = nilai.nis')
            ->join('guru', 'guru.id_guru = nilai.id_guru');

        if ($idGuru !== null) {
            $query->where('nilai.id_guru', $idGuru);
        }

        return $query->orderBy('banding_nilai.created_at', 'DESC')->findAll();
    }

    /**
     * Mendapatkan daftar banding nilai khusus untuk satu siswa berdasarkan NIS.
     * 
     * @param string $nis
     * @return array
     */
    public function getAppealsBySiswa($nis)
    {
        return $this->select('banding_nilai.*, banding_nilai.nilai_tugas_asal as nilai_tugas, banding_nilai.nilai_uts_asal as nilai_uts, banding_nilai.nilai_uas_asal as nilai_uas, banding_nilai.nilai_akhir_asal as nilai_akhir, guru.nama_guru, guru.mata_pelajaran')
            ->join('nilai', 'nilai.id = banding_nilai.id_nilai')
            ->join('guru', 'guru.id_guru = nilai.id_guru')
            ->where('nilai.nis', $nis)
            ->orderBy('banding_nilai.created_at', 'DESC')
            ->findAll();
    }
}
