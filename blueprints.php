<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$allowedExtensions = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'bmp'  => 'image/bmp',
    'svg'  => 'image/svg+xml',
    'tiff' => 'image/tiff',
    'ico'  => 'image/x-icon',
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['file']) || empty($_GET['file'])) {
        http_response_code(400); 
        exit('No file specified.');
    }

    $fileName = basename($_GET['file']);

    $uploadsDir = __DIR__ . '/uploads/blueprints/';

    $file_path = $uploadsDir . $fileName;

    if (!file_exists($file_path) || !is_file($file_path)) {
        http_response_code(404); 
        exit('File does not exist.');
    }

    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    if (!array_key_exists($extension, $allowedExtensions)) {
        http_response_code(415); 
        exit('Unsupported image type.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $actualMimeType = finfo_file($finfo, $file_path);
    finfo_close($finfo);

    if ($actualMimeType !== $allowedExtensions[$extension]) {
        http_response_code(415);
        exit('MIME type does not match file extension.');
    }

    header('Content-Type: ' . $actualMimeType);
    header('Content-Length: ' . filesize($file_path));
    header('Content-Disposition: inline; filename="' . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . '"');
    header('Cache-Control: public, max-age=86400'); 

    if (ob_get_level()) {
        ob_end_clean();
    }

    if (readfile($file_path) === false) {
        http_response_code(500);
        exit('Error reading the file.');
    }

    flush();
    exit();
} else {
    http_response_code(405); 
    header('Allow: GET');
    exit('Method not allowed.');
}
?>
