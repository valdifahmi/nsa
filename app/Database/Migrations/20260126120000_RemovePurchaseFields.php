<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePurchaseFields extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('tb_stock_in', 'grand_total');
        $this->forge->dropColumn('tb_stock_in', 'due_date');
        $this->forge->dropColumn('tb_stock_in', 'payment_status');
    }

    public function down()
    {
        $fields = [
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'after' => 'catatan',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'grand_total',
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Lunas', 'Belum Lunas'],
                'default' => 'Belum Lunas',
                'after' => 'due_date',
            ],
        ];
        $this->forge->addColumn('tb_stock_in', $fields);
    }
}
