<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOutServiceModel extends Model
{
    protected $table = 'tb_stock_out_services';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'stock_out_id',
        'service_id',
        'biaya_jasa',
        'jumlah',
        'pph_persen'
    ];

    // Timestamps
    protected $useTimestamps = false;

    // Validation rules
    protected $validationRules = [
        'stock_out_id' => 'required|integer',
        'service_id' => 'required|integer',
        'biaya_jasa' => 'required|numeric',
        'jumlah' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'stock_out_id' => [
            'required' => 'Stock out ID harus diisi',
            'integer' => 'Stock out ID harus berupa angka'
        ],
        'service_id' => [
            'required' => 'Service ID harus diisi',
            'integer' => 'Service ID harus berupa angka'
        ],
        'biaya_jasa' => [
            'required' => 'Biaya jasa harus diisi',
            'numeric' => 'Biaya jasa harus berupa angka'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'integer' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ]
    ];
}
