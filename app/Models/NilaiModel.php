<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table            = 'nilai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nis',
        'id_guru',
        'mata_pelajaran',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'status_kelulusan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil daftar nilai lengkap dengan informasi nama siswa, kelas, guru, dan mata pelajaran.
     * 
     * @return array
     */
    public function getNilaiWithDetails()
    {
        return $this->select('nilai.*, siswa.nama as nama_siswa, siswa.kelas, guru.nama_guru')
            ->join('siswa', 'siswa.nis = nilai.nis')
            ->join('guru', 'guru.id_guru = nilai.id_guru')
            ->orderBy('nilai.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Mengambil satu data nilai detail berdasarkan ID.
     * 
     * @param int $id
     * @return array|null
     */
    public function getNilaiByIdWithDetails($id)
    {
        return $this->select('nilai.*, siswa.nama as nama_siswa, siswa.kelas, guru.nama_guru')
            ->join('siswa', 'siswa.nis = nilai.nis')
            ->join('guru', 'guru.id_guru = nilai.id_guru')
            ->where('nilai.id', $id)
            ->first();
    }
}
