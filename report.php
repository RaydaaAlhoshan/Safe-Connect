<?php 
require "connect.php" ; 

session_start() ; 




if  ($_SERVER['REQUEST_METHOD'] == "GET" ) {
        
    if ( isset($_GET['specialist_id']  )  ) {


   
    $specialist_id = $_GET['specialist_id'] ; 
    $block_status = 1;
    
    $client_id = $_SESSION['users']['client']['id'] ; 
    $query = "UPDATE specialist SET blocked = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii',  $block_status  , $specialist_id);
    if ($stmt->execute()) {
      
        
        header("Location: plan_status.php ") ;
        exit() ; 
    }


    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
} else {
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}




?>