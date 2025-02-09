<?php
include 'connect.php'; // Include the database connection file
session_start();

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $experience = isset($_POST['experience']) ? trim($_POST['experience']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $certificate = isset($_FILES['certificate']['name']) ? $_FILES['certificate']['name'] : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($experience) || empty($bio)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }

    // Check if email already exists
    $email_check_stmt = $conn->prepare("SELECT id FROM specialist WHERE email = ?");
    if (!$email_check_stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    $email_check_stmt->bind_param("s", $email);
    $email_check_stmt->execute();
    $email_check_result = $email_check_stmt->get_result();
    if ($email_check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit();
    }
    $email_check_stmt->close();

    // Handle file upload
    if (!empty($certificate)) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Sanitize file name to prevent directory traversal attacks
        $certificate = basename($certificate);
        $certificate = preg_replace("/[^A-Za-z0-9\.\-_]/", '', $certificate);

        $target_file = $target_dir . $certificate;

        // Check file type (allow only specific types)
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Only PDF and image files are allowed for the certificate.']);
            exit();
        }

        // Check file size (e.g., max 2MB)
        if ($_FILES['certificate']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Certificate file size exceeds 2MB.']);
            exit();
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
            echo json_encode(['success' => false, 'message' => 'Error uploading certificate.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Certificate file is required.']);
        exit();
    }

    // Hash the password before saving
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the database with the hashed password and status pending
    $sql = "INSERT INTO specialist (name, email, password, experience, bio, certificate, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssss", $name, $email, $hashed_password, $experience, $bio, $certificate);

    if ($stmt->execute()) {
        // Retrieve the inserted user's ID
        $inserted_id = $stmt->insert_id;

        // Fetch the inserted user data
        $fetch_stmt = $conn->prepare("SELECT id, name, email, experience, bio, certificate, status FROM specialist WHERE id = ?");
        if (!$fetch_stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }

        $fetch_stmt->bind_param("i", $inserted_id);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();

        if ($result->num_rows === 1) {
            $specialist = $result->fetch_assoc();
            $_SESSION['users']['specialist'] = $specialist;

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Close statements and connection
            $fetch_stmt->close();
            $stmt->close();
            $conn->close();

            // Redirect to holdPage.html
            header("Location: holdPage.html");
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration successful, but failed to retrieve user data.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not complete the registration process.']);
        exit();
    }

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn) {
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
