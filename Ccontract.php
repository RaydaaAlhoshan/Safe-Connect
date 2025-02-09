<?php 
session_start(); // Start the session 
require 'connect.php'; 

// Configure error handling for production
ini_set('display_errors', 0); // Disable error display in production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '/path/to/your/error.log'); // Set the path to your error log

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Sanitize and validate inputs
    $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;
    $specialist_id = isset($_POST['specialist_id']) ? intval($_POST['specialist_id']) : 0;
    $place = isset($_POST['place']) ? htmlspecialchars(trim($_POST['place'])) : '';
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $width = isset($_POST['width']) ? floatval($_POST['width']) : 0.0;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0.0;
    $price_range = isset($_POST['price_range']) ? floatval($_POST['price_range']) : 0.0;
    $due_date = isset($_POST['due_date']) ? htmlspecialchars(trim($_POST['due_date'])) : '';
    $comments = isset($_POST['comments']) ? htmlspecialchars(trim($_POST['comments'])) : '';
    $blueprint_file = null; // Initialize as null

    $_SESSION['plan_data'] = [ 
        "client_id" => $client_id, 
        "specialist_id" => $specialist_id, 
        "place" => $place, 
        "name" => $name, 
        "width" => $width, 
        "height" => $height, 
        "price_range" => $price_range, 
        "due_date" => $due_date, 
        "comments" => $comments, 
        "blueprint" => null, // Default is null 
    ]; 

    // Handle file upload and store it 
    if (isset($_FILES['blueprint']) && $_FILES['blueprint']['error'] == 0) { 
        $file_tmp = $_FILES['blueprint']['tmp_name']; 
        $file_name = basename($_FILES['blueprint']['name']); 
        $target_dir = "uploads/blueprints/"; 
        $unique_prefix = uniqid('', true) . '_'; // Generate a unique prefix
        $blueprint_file = $target_dir . $unique_prefix . $file_name; 
        $file_type = strtolower(pathinfo($blueprint_file, PATHINFO_EXTENSION)); 

        // Allowed file formats 
        $allowed_formats = array("pdf", "doc", "docx", "png", "jpg", "jpeg", "gif"); 

        // Ensure directory exists 
        if (!is_dir($target_dir)) { 
            if (!mkdir($target_dir, 0755, true)) { // More secure permissions
                echo json_encode(['status' => "error", 'message' => "Failed to create upload directory."]);
                exit();
            }
        } 

        // Validate and move the uploaded file 
        if (in_array($file_type, $allowed_formats)) { 
            if (move_uploaded_file($file_tmp, $blueprint_file)) { 
                $_SESSION['plan_data']['blueprint'] = $blueprint_file; 

                // Begin Transaction
                $conn->begin_transaction();

                try {
                    // Insert the plan into the database 
                    $query = "INSERT INTO plan (client_id, specialist_id, place, name, width, height, price_range, due_date, comments, blueprint) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
                    $stmtPlan = $conn->prepare($query); 
                    if (!$stmtPlan) {
                        throw new Exception("Prepare failed for plan insertion: " . $conn->error);
                    }

                    $stmtPlan->bind_param(
                        "iissddssss", // Adjusted types based on data
                        $client_id, 
                        $specialist_id, 
                        $place, 
                        $name, 
                        $width, 
                        $height, 
                        $price_range, 
                        $due_date, 
                        $comments, 
                        $blueprint_file
                    ); 

                    if (!$stmtPlan->execute()) { 
                        throw new Exception("Plan insertion failed: " . $stmtPlan->error);
                    } 

                    $planId = intval($conn->insert_id);
                    $_SESSION['plan_data']['plan_id'] = $planId;

                    $stmtPlan->close();

                    // Insert into ratings table
                    $queryRatings = "INSERT INTO ratings (client_id, plan_id, specialist_id) VALUES (?, ?, ?)";
                    $stmtRatings = $conn->prepare($queryRatings);
                    if (!$stmtRatings) {
                        throw new Exception("Prepare failed for ratings insertion: " . $conn->error);
                    }

                    $stmtRatings->bind_param('iii', $client_id, $planId, $specialist_id);

                    if (!$stmtRatings->execute()) {
                        throw new Exception("Ratings insertion failed: " . $stmtRatings->error);
                    }

                    $stmtRatings->close();

                    // Insert into contract table
                    $content = ''; // Define as needed
                    $client_approved = 1; 
                    $specialist_approved = 0; 

                    $insertContract = "INSERT INTO contract (plan_id, client_id, specialist_id, content, client_approved, specialist_approved) 
                                       VALUES (?, ?, ?, ?, ?, ?)";   

                    $stmtContract = $conn->prepare($insertContract); 
                    if (!$stmtContract) {
                        throw new Exception("Prepare failed for contract insertion: " . $conn->error);
                    }

                    $stmtContract->bind_param('iiissi', $planId, $client_id, $specialist_id, $content, $client_approved, $specialist_approved);

                    if (!$stmtContract->execute()) {
                        throw new Exception("Contract insertion failed: " . $stmtContract->error);
                    }

                    $contractId = intval($conn->insert_id);
                    $_SESSION['plan_data']['contract_id'] = $contractId;

                    $stmtContract->close();

                    // Commit Transaction
                    $conn->commit();

                    // Redirect to plan_status.php
                    header("Location: plan_status.php");
                    exit(); 

                } catch (Exception $e) {
                    // Rollback Transaction on Error
                    $conn->rollback();
                    error_log($e->getMessage());
                    echo json_encode(['status' => "error", 'message' => $e->getMessage()]);
                    exit();
                }

            } else { 
                echo json_encode(['status' => "error", 'message' => "Failed to upload blueprint file."]);
                exit();
            } 
        } else { 
            echo json_encode(['status' => "error", 'message' => "Invalid file format. Only PDF, DOC, DOCX, PNG, JPG, JPEG, and GIF files are allowed."]);
            exit();
        } 
    } else { 
        echo json_encode(['status' => "error", 'message' => "No file was uploaded or there was an error during the upload."]);
        exit();
    } 
} else { 

    if (isset($_GET['planId'])) { 
        $plan_id = $_GET['planId']; 
        $payed_num = $_GET['payed_num'] ; 
 
  
        $client_id = $_SESSION['users']['client']['id'];
        $get_payment_query = "SELECT * from payment where client_id  =  {$client_id}" ; 
        $result = $conn->query($get_payment_query) ; 
      
        if ($result  && $result->num_rows > 0 ) 
        {
            
         $row = $result->fetch_assoc();  
         $amount = $row['amount'] ; 
         $account_number = $row['account_number'] ;   

        } 
    

        $sql = "SELECT  p.price_range   
                FROM specialist AS s  
                JOIN plan AS p  
                ON s.id = p.specialist_id  
                WHERE p.id = ? " ; 


        if ($stmt = $conn->prepare($sql)) { 
            // Bind the parameter 
            $stmt->bind_param('i', $plan_id ); 
            $stmt->execute(); 
            $stmt->bind_result($price); 
            // Fetch the data 
            if ($stmt->fetch()) { 
                // Redirect the user to the payment page with the variables in the URL 
                header("Location: payment.php?price=$price&planId=$plan_id&bankacc=$account_number&currentAmount=$amount&payment_num=$payed_num"); 
                exit(); // Always add exit after header to stop further execution 
            } else { 
                echo "No data found for the given planId."; 
            }
            $stmt->close(); 
        } else { 
            echo "Error preparing the query: " . $conn->error; 
        } 
    } 
} 
?>
