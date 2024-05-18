<?php
include_once "app/db.conn.php";

if(isset($_GET['file_name'])) {
    $file_name = $_GET['file_name'];
    $file_path = "upload/" . basename($file_name);

    if(file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Parameter file_name tidak ditemukan.";
}
?>