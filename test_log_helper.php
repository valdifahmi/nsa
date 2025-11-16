<?php

// Test LogHelper
require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = APPPATH . 'Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

// Start session
$session = \Config\Services::session();
$session->start();

// Set fake user session for testing
$session->set('user', [
    'id' => 1,
    'username' => 'admin',
    'nama_lengkap' => 'Administrator',
    'role' => 'admin'
]);

echo "Testing LogHelper...\n\n";

// Test 1: Check if LogHelper class exists
if (class_exists('LogHelper')) {
    echo "✓ LogHelper class exists\n";
} else {
    echo "✗ LogHelper class NOT found\n";
    echo "Trying to load helper manually...\n";
    helper('LogHelper');
}

// Test 2: Try to add a log
try {
    $result = LogHelper::add('TEST', 'TestModule', 123, 'This is a test log entry');
    if ($result) {
        echo "✓ Log added successfully\n";
    } else {
        echo "✗ Log add returned false\n";
    }
} catch (\Exception $e) {
    echo "✗ Error adding log: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// Test 3: Check if log was saved to database
try {
    $db = \Config\Database::connect();
    $builder = $db->table('tb_logs');
    $builder->where('action', 'TEST');
    $builder->where('module', 'TestModule');
    $log = $builder->get()->getRowArray();

    if ($log) {
        echo "✓ Log found in database:\n";
        print_r($log);
    } else {
        echo "✗ Log NOT found in database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking database: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
