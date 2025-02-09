<?php
session_start(); // بدء الجلسة
require 'connect.php';  // Include the database connection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $client_id = mysqli_real_escape_string($conn, $_SESSION['plan_data']['client_id']);
    $specialist_id = mysqli_real_escape_string($conn, $_SESSION['plan_data']['specialist_id']);
    $name = mysqli_real_escape_string($conn, $_SESSION['plan_data']['name']);
    $place = mysqli_real_escape_string($conn, $_SESSION['plan_data']['place']);
    $height = mysqli_real_escape_string($conn, $_SESSION['plan_data']['height']);
    $width = mysqli_real_escape_string($conn, $_SESSION['plan_data']['width']);
    $price_range = mysqli_real_escape_string($conn, $_SESSION['plan_data']['price_range']);
    $due_date = mysqli_real_escape_string($conn, $_SESSION['plan_data']['due_date']);
    $comments = mysqli_real_escape_string($conn, $_SESSION['plan_data']['comments']);
    $blueprint=$_SESSION['plan_data']["blueprint"];
    // echo   $client_id, $specialist_id, $name, $place, $height, $width, $price_range, $due_date, $blueprint, $comments;
    if (isset($_SESSION['plan_data'])) {
            if (isset($blueprint)) {
                $stmt = $conn->prepare("INSERT INTO plan (client_id, specialist_id, name, place, height, width, price_range, due_date, blueprint, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('iissdddsss', $client_id, $specialist_id, $name, $place, $height, $width, $price_range, $due_date, $blueprint, $comments);
    
                if ($stmt->execute()) {
                    echo "Form submitted successfully!";
                    header('Location: plan_status.php');  // Redirect to plan_status.php after submission
                    exit();
                } else {
                    echo "Error: <br>" . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
      
   
}else{
    echo "File Error";
}
    

    $conn->close();
}
?>
