<?php 
session_start(); 
require 'connect.php'; 

$id = $_SESSION['users']['specialist']['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['provide_plan']))    { 
    $plan_id = $_POST['plan_id']; 
 
    // Update the status in the database 
    $update_sql = "UPDATE plan SET status = 'ready' WHERE id = $plan_id AND specialist_id = $id"; 
    if ($conn->query($update_sql) ) { 
        
    } else { 
        echo "<p>Error updating plan status: " . $conn->error . "</p>"; 
    } 
} 

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['plan_file'])) {
    $plan_id = $_POST['plan_id'];
    $file = $_FILES['plan_file'];

    // Ensure a file is uploaded
    if ($file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/blueprints';
        $file_name = basename($file['name']);
        $target_file = $upload_dir . DIRECTORY_SEPARATOR . basename($file['name']);

        // Move the uploaded file to the server
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update the plan_file column in the database
            $update_sql = "UPDATE plan SET plan_file = ? WHERE id = ? AND specialist_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param('sii', $file_name, $plan_id, $id);
            if ($stmt->execute()) {
              
            } else {
                echo "<p>Error updating the database: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error moving the uploaded file.</p>";
        }
    } else {
        echo "<p>No file was uploaded or an error occurred.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Plan Progress</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" />

        <link rel="stylesheet" href="custom.css" />

        <link rel="stylesheet" href="plan_progress.css">
    </head>

    <body>
        <!-- Header -->
        <header>
            Specialist Dashboard
            <h3><a href="specialistHomePage.php">Home Page</a></h3>
        </header>

        <div class="container">
            <h2>Plan Progress</h2>

            <table>
                <thead>
                    <tr>
                        <th>Client Full Name</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                $sql = "SELECT p.name, p.due_date, p.id  , p.payed  
                FROM plan AS p
                INNER JOIN contract AS c 
                ON p.id = c.plan_id
                WHERE p.status = 'approved'  AND p.specialist_id = $id AND c.client_approved = 1 AND c.specialist_approved = 1 ";                
                $result = $conn->query($sql); 
                if ($result->num_rows > 0) { 
                    while ($row = $result->fetch_assoc()) { 
                        echo "<tr>"; 
                        echo "<td>" . $row['name'] . "</td>"; 
                        echo "<td>" . $row['due_date'] . "</td>"; 
                        echo "<td> 
                            <form method='POST' enctype='multipart/form-data'> 
                                <input type='hidden' name='plan_id' value='" . $row['id'] . "'> 
                                <label for='plan_file'>Upload Plan File:</label>
                                <input type='file' name='plan_file' required>
                                <button type='submit' name='provide_plan' " . ($row['payed'] ? '' : 'disabled') . ">Provide</button>

                    </form>
                    </td>";
                    echo "</tr>";
                    }
                    } else {
                    echo "<tr>
                        <td colspan='3'>No plans available</td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>

</html>