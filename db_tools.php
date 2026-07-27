<?php
require_once 'db.php';

// Configure your MySQL credentials and database name
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is empty
$db_name = 'school_db'; // Change to your database name if different

// Path to mysqldump / mysql binaries (Default for XAMPP on Windows)
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$mysql     = 'C:\\xampp\\mysql\\bin\\mysql.exe';

$action = $_GET['action'] ?? '';

// ============================================================================
// 1. EXPORT EMPTY SCHEMA ONLY
// ============================================================================
if ($action === 'export_schema') {
    $filename = "schema_" . date('Y-m-d') . ".sql";
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $cmd = "\"{$mysqldump}\" -h{$db_host} -u{$db_user} --no-data {$db_name}";
    passthru($cmd);
    exit;
}

// ============================================================================
// 2. EXPORT DATA ONLY
// ============================================================================
if ($action === 'export_data') {
    $filename = "data_" . date('Y-m-d') . ".sql";
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $cmd = "\"{$mysqldump}\" -h{$db_host} -u{$db_user} --no-create-info {$db_name}";
    passthru($cmd);
    exit;
}

// ============================================================================
// 3. IMPORT DATA / SCHEMA FROM FILE
// ============================================================================
if ($action === 'import' && isset($_FILES['sql_file'])) {
    $file = $_FILES['sql_file']['tmp_name'];

    if (!empty($file) && file_exists($file)) {
        $cmd = "\"{$mysql}\" -h{$db_host} -u{$db_user} {$db_name} < " . escapeshellarg($file);
        exec($cmd, $output, $return_var);

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        if ($return_var === 0) {
            header("Location: {$referer}?msg=import_success");
        } else {
            header("Location: {$referer}?msg=import_error");
        }
        exit;
    }
}
?>