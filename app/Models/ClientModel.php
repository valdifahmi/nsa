<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'tb_clients';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_klien', 'kontak', 'alamat'];
    protected $useTimestamps = false;

    /**
     * Get all clients for dropdown
     */
    public function getForDropdown()
    {
        return $this->select('id, nama_klien')
            ->orderBy('nama_klien', 'ASC')
            ->findAll();
    }

    /**
     * Get client with details
     */
    public function getClientWithDetails($id)
    {
        return $this->find($id);
    }

    /**
     * Check if client name already exists
     */
    public function isNameExists($name, $excludeId = null)
    {
        $builder = $this->where('nama_klien', $name);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
