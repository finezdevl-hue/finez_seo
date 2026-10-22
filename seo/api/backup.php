<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
// Administrative Access Only (Restricted from employees)
if (!isset($_SESSION['name']) || !isset($_SESSION['login_id']) || (int)$_SESSION['login_id'] !== 1) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

}

include_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['name'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$conn = get_db_connection();
$backupDir = realpath(__DIR__ . '/../../backups');
if (!$backupDir) {
    $dirToCreate = __DIR__ . '/../../backups';
    if (!is_dir($dirToCreate)) {
        mkdir($dirToCreate, 0755, true);
    }
    $backupDir = realpath($dirToCreate);
}

function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function time_elapsed_string($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d >= 1) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h >= 1) return $diff->h . ' hr' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i >= 1) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

    // =========================================================================
    // 1. List Server Backups & Summary Stats
    // =========================================================================
    if ($action == "list_backups") {
        try {
            $files = glob($backupDir . '/*.sql');
            $backups = [];
            $totalBytes = 0;
            $latestTime = 0;
            $latestFormatted = 'No backups found';

            if ($files) {
                // Sort newest first
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });

                foreach ($files as $filePath) {
                    $mtime = filemtime($filePath);
                    $size = filesize($filePath);
                    $totalBytes += $size;
                    if ($mtime > $latestTime) {
                        $latestTime = $mtime;
                        $latestFormatted = date('Y-m-d H:i:s', $mtime);
                    }

                    $filename = basename($filePath);
                    $createdDate = date('Y-m-d H:i:s', $mtime);

                    $backups[] = [
                        'filename' => $filename,
                        'size' => $size,
                        'size_formatted' => format_file_size($size),
                        'created_at' => $createdDate,
                        'created_ago' => time_elapsed_string($createdDate),
                        'download_url' => 'download_backup.php?file=' . urlencode($filename),
                        'server_path' => $filePath
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'backups' => $backups,
                'total_count' => count($backups),
                'total_size_formatted' => format_file_size($totalBytes),
                'latest_backup' => $latestFormatted,
                'server_folder' => $backupDir
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 2. Create & Save Database Backup on Server
    // =========================================================================
    if ($action == "create_backup") {
        try {
            // Increase execution time for export
            @set_time_limit(300);

            $timestamp = date('Y-m-d_H-i-s');
            $filename = "finez_seo_backup_{$timestamp}.sql";
            $outputFile = $backupDir . DIRECTORY_SEPARATOR . $filename;

            $tables = [];
            $res = $conn->query("SHOW TABLES");
            while ($row = $res->fetch_array()) {
                $tables[] = $row[0];
            }

            $handle = fopen($outputFile, 'w');
            if (!$handle) {
                throw new Exception("Unable to open backup file for writing at: " . $outputFile);
            }

            // Write header
            $header = "-- ==========================================================\n"
                    . "-- Finez Platform Database Backup\n"
                    . "-- Generated on: " . date('Y-m-d H:i:s') . "\n"
                    . "-- Host: 127.0.0.1 | Database: finez_seo\n"
                    . "-- Total Tables: " . count($tables) . "\n"
                    . "-- ==========================================================\n\n"
                    . "SET FOREIGN_KEY_CHECKS=0;\n"
                    . "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n"
                    . "SET time_zone = \"+00:00\";\n\n";
            fwrite($handle, $header);

            foreach ($tables as $table) {
                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for table `{$table}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

                $createRes = $conn->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createRes->fetch_array();
                fwrite($handle, $createRow[1] . ";\n\n");

                // Dump table rows in batches
                $countRes = $conn->query("SELECT COUNT(*) AS c FROM `{$table}`");
                $count = (int)$countRes->fetch_assoc()['c'];

                if ($count > 0) {
                    fwrite($handle, "-- Dumping data for table `{$table}` (" . $count . " rows)\n");
                    $batchSize = 250;
                    for ($offset = 0; $offset < $count; $offset += $batchSize) {
                        $dataRes = $conn->query("SELECT * FROM `{$table}` LIMIT {$offset}, {$batchSize}");
                        
                        $valuesArr = [];
                        while ($row = $dataRes->fetch_assoc()) {
                            $escapedVals = [];
                            foreach ($row as $val) {
                                if (is_null($val)) {
                                    $escapedVals[] = "NULL";
                                } else {
                                    $escapedVals[] = "'" . $conn->real_escape_string($val) . "'";
                                }
                            }
                            $valuesArr[] = "(" . implode(", ", $escapedVals) . ")";
                        }

                        if (!empty($valuesArr)) {
                            $insertSql = "INSERT INTO `{$table}` VALUES \n" . implode(",\n", $valuesArr) . ";\n";
                            fwrite($handle, $insertSql);
                        }
                    }
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n\n-- Backup completed successfully on " . date('Y-m-d H:i:s') . "\n");
            fclose($handle);

            $fileSize = filesize($outputFile);

            echo json_encode([
                'status' => 'success',
                'message' => 'Database backup generated and stored successfully in the server folder!',
                'filename' => $filename,
                'size_formatted' => format_file_size($fileSize),
                'download_url' => 'download_backup.php?file=' . urlencode($filename),
                'server_path' => $outputFile
            ]);
            exit();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // =========================================================================
    // 3. Delete Backup File from Server
    // =========================================================================
    if ($action == "delete_backup") {
        try {
            $filename = isset($_POST['filename']) ? basename($_POST['filename']) : '';

            if (empty($filename) || !preg_match('/^[a-zA-Z0-9_\-\.]+\.sql$/', $filename)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid filename']);
                exit();
            }

            $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($filePath) && strpos(realpath($filePath), $backupDir) === 0) {
                unlink($filePath);
                echo json_encode(['status' => 'success', 'message' => "Backup file '$filename' was removed from the server."]);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'File not found on server.']);
                exit();
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }
}
?>
