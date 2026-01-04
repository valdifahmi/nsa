<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'tb_suppliers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_supplier', 'kontak', 'alamat'];
    protected $useTimestamps = false;

    /**
     * Get all suppliers for dropdown
     */
    public function getForDropdown()
    {
        return $this->select('id, nama_supplier')
            ->orderBy('nama_supplier', 'ASC')
            ->findAll();
    }

    /**
     * Get supplier with details
     */
    public function getSupplierWithDetails($id)
    {
        return $this->find($id);
    }

    /**
     * Check if supplier name already exists
     */
    public function isNameExists($name, $excludeId = null)
    {
        $builder = $this->where('nama_supplier', $name);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
