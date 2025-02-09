<?php
session_start();
include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    if (empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'الرجاء تعبئة جميع الحقول']);
        exit();
    }

    $allowedRoles = ['client', 'specialist']; 
    if (!in_array($role, $allowedRoles)) {
        echo json_encode(['success' => false, 'message' => 'دور غير صالح']);
        exit();
    }

    $table = $role === 'client' ? 'client' : 'specialist';

    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE email = ?");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'خطأ في تجهيز الطلب: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if (!isset($_SESSION['users'])) {
                $_SESSION['users'] = [];
            }

            $_SESSION['users'][$role] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'], 
            ];

            if (!isset($_SESSION['active_roles'])) {
                $_SESSION['active_roles'] = [];
            }

            if (!in_array($role, $_SESSION['active_roles'])) {
                $_SESSION['active_roles'][] = $role;
            }

            echo json_encode(['success' => true, 'role' => $role]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'كلمة المرور غير صحيحة']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'المستخدم غير موجود']);
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صحيحة']);
}
?>
