<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\ActivityLogModel;

class PricingController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->activityLogModel = new ActivityLogModel();
        helper(['form', 'url']);
    }

    /**
     * Display pricing management page (Admin only)
     */
    public function index()
    {
        // Get categories and brands for bulk mark-up filter
        $data = [
            'title' => 'Manajemen Harga',
            'categories' => $this->categoryModel->findAll(),
            'brands' => $this->brandModel->findAll()
        ];

        return view('Pricing/index', $data);
    }

    /**
     * Fetch all products with pricing (AJAX)
     */
    public function fetchAll()
    {
        try {
            // Get all products with category and brand info
            $products = $this->productModel
                ->select('tb_products.*, tb_categories.nama_kategori, tb_brands.nama_brand')
                ->join('tb_categories', 'tb_categories.id = tb_products.category_id', 'left')
                ->join('tb_brands', 'tb_brands.id = tb_products.brand_id', 'left')
                ->orderBy('tb_products.id', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error fetching products: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update single product price (AJAX)
     */
    public function updatePrice()
    {
        try {
            $json = $this->request->getJSON(true);

            if (!$json) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid JSON data'
                ]);
            }

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'product_id' => 'required|integer',
                'harga_beli_baru' => 'required|decimal',
                'harga_jual_baru' => 'required|decimal'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }

            $productId = $json['product_id'];
            $hargaBeliBaru = $json['harga_beli_baru'];
            $hargaJualBaru = $json['harga_jual_baru'];

            // Get product info
            $product = $this->productModel->find($productId);
            if (!$product) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }

            // Store old prices for logging
            $hargaBeliLama = $product['harga_beli_saat_ini'];
            $hargaJualLama = $product['harga_jual_saat_ini'];

            // Update prices
            $updateData = [
                'harga_beli_saat_ini' => $hargaBeliBaru,
                'harga_jual_saat_ini' => $hargaJualBaru
            ];

            if ($this->productModel->update($productId, $updateData)) {
                // Log activity
                $user = session()->get('user');
                $this->activityLogModel->insert([
                    'user_id' => $user['id'],
                    'action' => 'UPDATE',
                    'module' => 'Pricing',
                    'log_message' => "Updated price for product: {$product['nama_barang']} (Code: {$product['kode_barang']}). " .
                        "Harga Beli: Rp " . number_format($hargaBeliLama, 0, ',', '.') . " → Rp " . number_format($hargaBeliBaru, 0, ',', '.') . ". " .
                        "Harga Jual: Rp " . number_format($hargaJualLama, 0, ',', '.') . " → Rp " . number_format($hargaJualBaru, 0, ',', '.')
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Harga berhasil diupdate'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update price'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update price error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk mark-up by category or brand (AJAX)
     */
    public function bulkMarkUp()
    {
        try {
            $json = $this->request->getJSON(true);

            if (!$json) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid JSON data'
                ]);
            }

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'filter_type' => 'required|in_list[category,brand,all]',
                'filter_id' => 'permit_empty|integer',
                'markup_percentage' => 'required|decimal',
                'markup_method' => 'required|in_list[flat,addition]'
            ]);

            if (!$validation->run($json)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }

            $filterType = $json['filter_type'];
            $filterId = $json['filter_id'] ?? null;
            $markupPercentage = $json['markup_percentage'];
            $markupMethod = $json['markup_method'];

            // Build query based on filter
            $builder = $this->productModel->builder();

            if ($filterType === 'category' && $filterId) {
                $builder->where('category_id', $filterId);
                $filterName = $this->categoryModel->find($filterId)['nama_kategori'] ?? 'Unknown';
            } elseif ($filterType === 'brand' && $filterId) {
                $builder->where('brand_id', $filterId);
                $filterName = $this->brandModel->find($filterId)['nama_brand'] ?? 'Unknown';
            } else {
                $filterName = 'All Products';
            }

            // Get products to update
            $products = $builder->get()->getResultArray();

            if (empty($products)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No products found with selected filter'
                ]);
            }

            $updatedCount = 0;
            $skippedCount = 0;
            $totalProducts = count($products);

            // Update each product
            foreach ($products as $product) {
                $hargaBeli = $product['harga_beli_saat_ini'];
                $hargaJualLama = $product['harga_jual_saat_ini'];

                // Skip if harga beli = 0 (prevent division by zero)
                if ($hargaBeli <= 0) {
                    $skippedCount++;
                    continue;
                }

                $hargaJualBaru = 0;

                if ($markupMethod === 'flat') {
                    // FLAT: Set margin sama untuk semua produk
                    // Harga_Jual_Baru = Harga_Beli * (1 + Markup%/100)
                    $hargaJualBaru = $hargaBeli * (1 + ($markupPercentage / 100));
                } elseif ($markupMethod === 'addition') {
                    // ADDITION: Tambahkan ke margin existing
                    // Margin_Sekarang = ((Harga_Jual - Harga_Beli) / Harga_Beli) * 100
                    $marginSekarang = (($hargaJualLama - $hargaBeli) / $hargaBeli) * 100;

                    // Margin_Baru = Margin_Sekarang + Nilai_Input
                    $marginBaru = $marginSekarang + $markupPercentage;

                    // Harga_Jual_Baru = Harga_Beli * (1 + Margin_Baru/100)
                    $hargaJualBaru = $hargaBeli * (1 + ($marginBaru / 100));
                }

                // Round up to avoid decimals (for BIGINT compatibility)
                $hargaJualBaru = ceil($hargaJualBaru);

                // Update
                $this->productModel->update($product['id'], [
                    'harga_jual_saat_ini' => $hargaJualBaru
                ]);

                $updatedCount++;
            }

            // Log activity
            $user = session()->get('user');
            $methodText = $markupMethod === 'flat' ? 'Flat' : 'Addition';
            $this->activityLogModel->insert([
                'user_id' => $user['id'],
                'action' => 'UPDATE',
                'module' => 'Pricing',
                'log_message' => "Applied {$methodText} mark-up {$markupPercentage}% to {$updatedCount} products. Filter: {$filterType} = {$filterName}. Skipped: {$skippedCount} products (harga beli = 0)"
            ]);

            $message = "Berhasil mengupdate {$updatedCount} dari {$totalProducts} produk dengan mark-up {$methodText} {$markupPercentage}%";
            if ($skippedCount > 0) {
                $message .= ". {$skippedCount} produk dilewati (harga beli = 0)";
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'total_products' => $totalProducts
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Bulk mark-up error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
