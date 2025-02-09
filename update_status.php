<?php
session_start();
require 'connect.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function UpdateStatus($conn , $query , $status , $id  )
{
    if ( $stmt = $conn->prepare($query)  )
    {   
            $stmt->bind_param('si' ,  $status , $id    ) ; 
            $stmt->execute() ; 
          
                
    } else {
            echo json_encode(['status' => "failed"]); 
    } 
} 


// Handle the admin's action (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST'  ) {
     $data = json_decode(file_get_contents('php://input'), true); // Decode JSON input
    $action = $data['action']  ??  '' ; 
    $id = intval($data['id'])  ;
    $who = $data['who'] ; 
    if (!in_array($action, ['approved', 'decliend', 'pending']) || !$id || !$who) {
        echo json_encode(['status' => 'failed', 'message' => 'Invalid input']);
        exit;
    }

    $status = $action  ; 
    

    
    if ($who == "plan" )
    {
        

        // $getIds =  "SELECT client_id  , specialist_id FROM plan WHERE id =  ?  " ; 
        // $stmtGetIds = $conn->prepare($getIds);
        // if (!$stmtGetIds) {
        //     echo json_encode(['status' => "error", 'message' => "فشل في إعداد الاستعلام: " . $conn->error]);
        //     exit();
        // }
        // $stmtGetIds->bind_param('i', $id);
        // $stmtGetIds->execute();
        // $result = $stmtGetIds->get_result();
        // $row = $result->fetch_assoc();
        // if (!$row) {
        //     echo json_encode(['status' => "error", 'message' => "الخطة غير موجودة أو ليست في حالة المعالجة."]);
        //     exit();
        // }
        
        // $stmtGetIds->close();
        
        // var_dump($_SESSION) ;
        
        // echo json_encode(['status' =>  $row  ]);        


     
        $specialist_approved = 1 ; 
        $client_approved = 1 ; 

        $checkContract = "SELECT c.*  FROM contract as  c INNER JOIN plan as p ON p.id = c.plan_id   WHERE c.plan_id = ? "; 
        $stmtCheckContract = $conn->prepare($checkContract);
        if (!$stmtCheckContract) {
            echo json_encode(['status' => "error", 'message' => "فشل في إعداد الاستعلام: " . $conn->error]);
            exit();
        }
        $stmtCheckContract->bind_param('i', $id);
        $stmtCheckContract->execute();
        $contract_data = $stmtCheckContract->get_result()->fetch_assoc()  ; 
        $contract_id = intval($contract_data['id'] ) ; 
        $stmtCheckContract->close();
        
        


        $updateContract = "UPDATE contract 
                           SET  client_approved = ?, specialist_approved = ? 
                           WHERE id = ?";   
        
        $stmt = $conn->prepare($updateContract); 
        if (!$stmt) {
            echo json_encode(['status' => "error", 'message' => "فشل في إعداد الاستعلام: " . $conn->error]);
            exit();
        }
        $stmt->bind_param('iii',  $client_approved, $specialist_approved, $contract_id);
        if (!$stmt->execute()) 
        {
            echo json_encode(['status' => "error", 'message' => "فشل في تنفيذ الاستعلام: " . $stmt->error]);
        } 
        $stmt->close();



        $update_plan_query  = "UPDATE plan set status =  (?)  where  id = ? " ; 
        UpdateStatus($conn  ,  $update_plan_query  , $status , $id ) ; 
        

        // $update_rating_query   = "UPDATE ratings  set plan_id = {$id}  where  specialist_id  = {$specialist_id} " ; 
        // $conn->query($update_rating_query) ; 
    
    }
    else if($who == "admin") {
        
        $query = "UPDATE specialist set status =  (?)  where  id = ? " ; 
        UpdateStatus($conn  ,  $query  , $status , $id ) ; 

     
    }  
   


    
    $_SESSION['status'] = $status;
    echo json_encode(['status' => $status ]);
    exit;
}


else if  ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_SESSION['users']['specialist']['id']  ; 
    $stmt = $conn->query("SELECT status from specialist where id = $id ") ; 
    $result = $stmt->fetch_assoc() ; 
    $status = $result['status'] ?? 'pending' ; 
    echo json_encode(['status' =>  $status  ]);
    exit;
} else 
echo json_encode(['error' => 'Invalid request.']);

// Invalid request
?>