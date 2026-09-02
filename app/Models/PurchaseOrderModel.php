<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_order';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nomor_po', 'supplier_id', 'produk_id', 'jumlah', 'tanggal', 'total_harga', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getPoWithSupplier()
    {
        return $this->select('purchase_order.*, supplier.nama_supplier')
                    ->join('supplier', 'supplier.id = purchase_order.supplier_id')
                    ->orderBy('purchase_order.tanggal', 'DESC')
                    ->findAll();
    }
}
