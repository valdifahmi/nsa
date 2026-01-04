<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOutModel extends Model
{
    protected $table = 'tb_stock_out';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'nomor_transaksi',
        'nomor_invoice',
        'user_id',
        'client_id',
        'tanggal_keluar',
        'penerima',
        'catatan',
        'tipe_transaksi',
        'status_work_order',
        'ppn_persen',
        'pph_persen',
        'total_barang',
        'total_jasa',
        'total_ppn',
        'total_pph',
        'grand_total',
        'status_pembayaran'
    ];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
