<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['name'])) {
    http_response_code(401);
    die("Access denied. Please log in.");
}

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';

if (empty($filename) || !preg_match('/^[a-zA-Z0-9_\-\.]+\.sql$/', $filename)) {
    http_response_code(400);
    die("Invalid filename specified.");
}

$backupDir = realpath(__DIR__ . '/../backups');
$filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

if (!$backupDir || !file_exists($filePath) || strpos(realpath($filePath), $backupDir) !== 0) {
    http_response_code(404);
    die("Backup file not found on server.");
}

// Clear output buffers
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit();
