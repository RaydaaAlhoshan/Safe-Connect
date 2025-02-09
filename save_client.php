<?php
session_start();

// Enable error reporting to catch any issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include 'connect.php'; 

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and ensure they are properly retrieved
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $account_num = isset($_POST['account_num']) ? $_POST['account_num'] : '';

    // Check if all form data is retrieved correctly
    if (empty($name) || empty($email) || empty($password)) {
        echo "Error: Missing required fields.";
        exit();
    }

    // Hash the password before saving it to the database for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare the SQL statement using prepared statements to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO client (name, email, password) VALUES (?, ?, ?)");

    // Check if the prepare statement was successful
    if ($stmt === false) {
        echo "Error preparing the statement: " . $conn->error;
        exit();
    }

    // Bind the parameters and execute the statement
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {

        $last_id = $conn->insert_id ; 
        
        $stmt = $conn->prepare("INSERT  INTO payment    (client_id ) VALUES (?)  ");
        $stmt->bind_param('i' , $last_id  ); 
        $stmt->execute()  ; 
        
        

        
        $stmt = $conn->prepare("SELECT * FROM client WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $client = $result->fetch_assoc(); // Fetch the client data

                // Store client data in the session
                $_SESSION['users']['client'] = $client;
          
                // Redirect to the client home page after successful submission
                header("Location: clientHomePage.php");
                exit();
            } else {
                echo "Error: Client not found after insertion.";
            }
        } else {
            echo "Error fetching the client data: " . $stmt->error;
        }
    } else {
        // Output detailed error message
        echo "Error executing the query: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Error: Invalid request method.";
}
?>