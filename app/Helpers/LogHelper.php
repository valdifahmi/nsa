<?php

use App\Models\LogModel;

if (!function_exists('add_log')) {
    /**
     * Add log entry to database
     * 
     * @param string $action Action performed (CREATE, UPDATE, DELETE, LOGIN, LOGOUT, etc.)
     * @param string $module Module name (Category, Product, Brand, Purchase, Sale, etc.)
     * @param int|null $record_id ID of the record affected (optional)
     * @param string|null $log_message Detailed log message (optional)
     * @return bool True if log saved successfully, false otherwise
     */
    function add_log($action, $module, $record_id = null, $log_message = null)
    {
        try {
            // Get user_id from session
            $session = session();
            $user_id = null;

            if ($session->has('user')) {
                $user = $session->get('user');
                $user_id = $user['id'] ?? null;
            }

            // Prepare log data
            $data = [
                'user_id' => $user_id,
                'action' => strtoupper($action),
                'module' => $module,
                'record_id' => $record_id,
                'log_message' => $log_message,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Save to database
            $logModel = new LogModel();
            return $logModel->insert($data) !== false;
        } catch (\Exception $e) {
            // Log error but don't break the application
            log_message('error', 'LogHelper Error: ' . $e->getMessage());
            return false;
        }
    }
}
