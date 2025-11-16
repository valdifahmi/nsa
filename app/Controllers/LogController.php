<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LogModel;

class LogController extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
        helper(['form', 'url']);
    }

    /**
     * Display the log viewer page (Admin only)
     */
    public function index()
    {
        // Get database connection for JOIN query
        $db = \Config\Database::connect();
        $builder = $db->table('tb_logs l');

        // Select logs with user information
        $builder->select('l.*, u.username, u.nama_lengkap');
        $builder->join('tb_users u', 'l.user_id = u.id', 'left');
        $builder->orderBy('l.created_at', 'DESC');

        // Get all logs
        $logs = $builder->get()->getResultArray();

        // Pass data to view
        $data = [
            'title' => 'System Logs',
            'logs' => $logs
        ];

        return view('Log/index', $data);
    }
}
