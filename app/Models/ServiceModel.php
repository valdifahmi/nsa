<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table = 'tb_services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nama_jasa',
        'harga_standar',
        'deskripsi'
    ];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    // Validation rules
    protected $validationRules = [
        'nama_jasa' => 'required|max_length[255]',
        'harga_standar' => 'required|integer'
    ];

    protected $validationMessages = [
        'nama_jasa' => [
            'required' => 'Nama jasa harus diisi',
            'max_length' => 'Nama jasa maksimal 255 karakter'
        ],
        'harga_standar' => [
            'required' => 'Harga standar harus diisi',
            'integer' => 'Harga standar harus berupa angka'
        ]
    ];
}
