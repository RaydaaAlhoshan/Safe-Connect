<?php
session_start();
require 'connect.php';

// Fetch plans related to the specialist's work
$specialist_id = $_SESSION['users']['specialist']['id'];

$sql = "SELECT p.* , sp.blocked  FROM plan as p  INNER JOIN specialist as sp ON sp.id = p.specialist_id   WHERE specialist_id = '$specialist_id' AND p.status='processing'  AND payed = 0 ";
$result = $conn->query($sql);

$plans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // echo var_dump($row) ; 
        // exit() ; 
        
     
        $plans[] = [
            'id' => $row['id'],
            'place' => $row['place'],
            'due_date' => $row['due_date'],
            'comments' => $row['comments'],
            'blueprint' => $row['blueprint'],
            'blocked' => $row['blocked'] , 
            'height' => $row['height'] , 
            'width' => $row['width'] , 
            'price' => $row['price_range']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($plans);
?>