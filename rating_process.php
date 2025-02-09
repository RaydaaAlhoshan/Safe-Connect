<?php 
require "connect.php" ; 

session_start() ; 




if  ($_SERVER['REQUEST_METHOD'] == "GET" ) {
    
    if ( isset($_GET['rate']  , $_GET['plan_id'] , $_GET['client_id'] )   ) {

    $rate = $_GET['rate'] ; 
    
    $clientId  = $_GET['client_id'] ; 
    
    $spId  = $_GET['specialist_id'] ; 

    $plan_id = $_GET['plan_id']  ; 
    

    // $query = "UPDATE ratings  SET rating = ? , client_id = ? , plan_id = ? ,  specialist_id = ?  WHERE plan_id = ?";

    $query = "UPDATE ratings  SET rating = ?  WHERE plan_id = ? AND client_id = ?  ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $rate, $plan_id  , $clientId  );
    if ($stmt->execute()) {
        
        
   
       
        header("Location: plan_status.php ") ;

        // echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
} else {
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}




?>