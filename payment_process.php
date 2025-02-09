<?php 
require "connect.php"; 

session_start(); 


if (!isset($_SESSION['users']['client']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $required_fields = ['plan_id', 'expiry_date', 'card_num', 'cvv', 'price', 'payment_num'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            exit();
        }
    }

    $planId = intval($_POST['plan_id']);
    $client_id = intval($_SESSION['users']['id']);
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
    $card_number  = $conn->real_escape_string($_POST['card_num']);
    $cvv = $conn->real_escape_string($_POST['cvv']);
    $amount_t_pay = floatval($_POST['price']);
    $payment_num = intval($_POST['payment_num']) + 1 ; 

    $conn->begin_transaction();

    try {
        $query = "UPDATE plan SET  payed = ?  WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Preparation failed: " . $conn->error);
        }
        $stmt->bind_param('ii', $payment_num ,  $planId )  ;
        if (!$stmt->execute()) {
            throw new Exception("Execution failed: " . $stmt->error);
        }
        $stmt->close();

        $payment_query = "UPDATE payment SET amount = ?, account_number = ?, expiry_date = ?, cvv = ? WHERE client_id = ?";
        $stmt = $conn->prepare($payment_query);
        if (!$stmt) {
            throw new Exception("Preparation failed: " . $conn->error);
        }
        $stmt->bind_param('dsssi', $amount_t_pay, $card_number, $expiry_date, $cvv, $client_id);
        if (!$stmt->execute()) {
            throw new Exception("Execution failed: " . $stmt->error);
        }
        $stmt->close();

        // $plan_update_query  = "UPDATE plan SET payed = ? WHERE id = ?";
        // $stmt = $conn->prepare($plan_update_query);
        // if (!$stmt) {
        //     throw new Exception("Preparation failed: " . $conn->error);
        // }
        // $stmt->bind_param('ii', $payment_num, $planId);
        // if (!$stmt->execute()) {
        //     throw new Exception("Execution failed: " . $stmt->error);
        // }
        // $stmt->close();

        $conn->commit();

        header("Location: successfully_page.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء معالجة طلبك. حاول مرة أخرى لاحقًا.']);
        exit();
    }

} else {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صالحة.']);
}
?>