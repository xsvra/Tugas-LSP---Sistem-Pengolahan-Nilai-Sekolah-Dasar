<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id_siswa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_siswa',
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kelas',
        'tahun_masuk',
        'nama_wali',
        'no_hp_wali',
        'status_siswa',
        'foto'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nis'           => 'required|numeric|exact_length[10]',
        'nisn'          => 'required|numeric|exact_length[10]',
        'nama'          => 'required|min_length[2]|max_length[100]',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'tempat_lahir'  => 'permit_empty|max_length[100]',
        'tanggal_lahir' => 'permit_empty|valid_date[Y-m-d]',
        'alamat'        => 'permit_empty',
        'kelas'         => 'required|in_list[1,2,3,4,5,6]',
        'tahun_masuk'   => 'permit_empty|exact_length[4]|numeric',
        'nama_wali'     => 'permit_empty|max_length[100]',
        'no_hp_wali'    => 'permit_empty|max_length[20]',
        'status_siswa'  => 'permit_empty|max_length[50]',
        'foto'          => 'permit_empty|max_length[255]'
    ];
    protected $validationMessages   = [
        'nis' => [
            'is_unique' => 'NIS sudah terdaftar di database.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
