<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id_guru';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_guru',
        'nik',
        'nama_guru',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'email',
        'mata_pelajaran',
        'kelas_diajar',
        'pendidikan_terakhir',
        'status_kepegawaian',
        'tanggal_masuk',
        'foto'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_guru'             => 'required|integer',
        'nik'                 => 'permit_empty',
        'nama_guru'           => 'permit_empty|max_length[100]',
        'jenis_kelamin'       => 'permit_empty|in_list[L,P]',
        'tempat_lahir'        => 'permit_empty|max_length[100]',
        'tanggal_lahir'       => 'permit_empty|valid_date[Y-m-d]',
        'alamat'              => 'permit_empty',
        'no_hp'               => 'permit_empty|max_length[20]',
        'email'               => 'permit_empty|valid_email|max_length[100]',
        'mata_pelajaran'      => 'required|min_length[2]|max_length[255]',
        'kelas_diajar'        => 'permit_empty|max_length[255]',
        'pendidikan_terakhir' => 'permit_empty|max_length[50]',
        'status_kepegawaian'  => 'permit_empty|max_length[50]',
        'tanggal_masuk'       => 'permit_empty|valid_date[Y-m-d]',
        'foto'                => 'permit_empty|max_length[255]'
    ];
    protected $validationMessages   = [
        'id_guru' => [
            'is_unique' => 'ID Guru sudah terdaftar.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
