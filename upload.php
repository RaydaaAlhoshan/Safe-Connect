<?php
session_start();
require 'connect.php'; // تأكد من استيراد ملف الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تأكد من أن هناك ملف تم رفعه
    if (isset($_FILES['plan_file']) && isset($_POST['client_id'])) {
        $clientId = intval($_POST['client_id']);
        $file = $_FILES['plan_file'];

        // تحقق من رفع الملف بنجاح
        if ($file['error'] === UPLOAD_ERR_OK) {
            // إعداد اسم الملف والمكان الذي سيتم رفعه إليه
            $uploadDir = 'uploads/plans/'; // تأكد من وجود هذا المجلد وامتلاكه للصلاحيات اللازمة
            $uploadFilePath = $uploadDir . basename($file['name']);

            // رفع الملف إلى المجلد المحدد
            if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                // تحديث حالة الخطة إلى "ready" في قاعدة البيانات
                $sql = "UPDATE plan SET status = 'ready', plan_file = '$uploadFilePath' WHERE id = $clientId";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update plan status.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload error.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
