<?php
session_start();
require 'connect.php';

// Check if the user is logged in and has the 'specialist' role
if (isset($_SESSION['users']['specialist'])) {
    header('Location: specialistHomePage.php');
    exit();
}

// Check if the user is logged in as a 'client'
if (!isset($_SESSION['users']['client']['id'])) {
    // Redirect to login page or show an error
    header('Location: login.php');
    exit();
}

$client_id = $_SESSION['users']['client']['id'];

// Prepare the SQL statement using prepared statements to prevent SQL injection
$sql = "SELECT 
            sp.name As name , 
            plan.id AS plan_id, 
            plan.status, 
            plan.plan_file, 
            ratings.rating, 
            plan.specialist_id, 
            sp.name AS specialist_name, 
            plan.payed, 
            c.specialist_approved, 
            sp.blocked, 
            plan.due_date, 
            plan.starting_date,
            sp.bio,
            sp.experience
        FROM 
            plan  
        LEFT JOIN 
            ratings ON plan.id = ratings.plan_id 
        INNER   JOIN 
            contract AS c ON c.id = plan.client_id  
        INNER JOIN 
            specialist AS sp ON plan.specialist_id = sp.id  
        WHERE 
            plan.client_id = ? 
        ORDER BY plan.id 
            ";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // Handle statement preparation error
    echo "Error preparing statement: " . htmlspecialchars($conn->error);
    exit();
}

$stmt->bind_param("i", $client_id);

if (!$stmt->execute()) {
    // Handle execution error
    echo "Error executing query: " . htmlspecialchars($stmt->error);
    exit();
}

$result = $stmt->get_result();
$plans_data = [];

if ($result->num_rows > 0) {
    while ($plan = $result->fetch_assoc()) {
        $plans_data[] = [
            'name' => $plan['name']  , 
            'id' => $plan['plan_id'],
            'status' => $plan['status'],
            'plan_file_exists' => !empty($plan['plan_file']) ? 1 : 0,
            'plan_file' => ($plan['plan_file'])  , 
            'rate' => isset($plan['rating']) ? $plan['rating'] : null,
            'specialist_id' => $plan['specialist_id'],
            'payed' => $plan['payed'],
            'specialist_approved' => !empty($plan['specialist_approved']) ? 1 : 0,
            'blocked' => $plan['blocked'] ,
            'sp_name' => $plan['specialist_name'],
            'due_date' => $plan['due_date'],
            'starting_date' => $plan['starting_date'],
            'bio' => $plan['bio'],
            'experience' => $plan['experience']
        ];
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Status</title>
    <link rel="stylesheet" href="plan_status.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        button:disabled {
            background-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
            opacity: 0.6;
            color: black;
        }
    </style>
</head>

<body>
    <div id="plans-container" class="container mt-5">
        <?php if (!empty($plans_data)) { ?>
        <?php } else { ?>
            <p id="status-message" class="status">Status: <span id="status-text">No Plans yet</span></p>
        <?php } ?>
    </div>

    <script>
        const plansData = <?php echo json_encode($plans_data); ?>;
    </script>
    <script>
        function updateStatusUI(plan) {
            const container = document.createElement('div');
            container.classList.add('status-container', 'border', 'p-4', 'mb-4', 'rounded');

            const header = document.createElement('h2');
            header.classList.add('status-header');
            header.textContent = `Plan ${plan.id}`;
            container.appendChild(header);

            const statusMessage = document.createElement('p');
            statusMessage.classList.add('status');
            statusMessage.innerHTML =
                `Status: <span>${plan.status}</span>`;
            container.appendChild(statusMessage);

            const imagePlan = document.createElement('img');
            imagePlan.setAttribute('id', `planImage-${plan.id}`);
            imagePlan.setAttribute('src', `blueprints.php?file=${(plan.plan_file)}`);
            imagePlan.style.width = "100%";
            imagePlan.style.height = "auto";
            imagePlan.style.display = "none";
            imagePlan.style.margin = "10px 0";
            container.appendChild(imagePlan);
            console.log(plan.blocked) ; 
                const viewPlanBtn = document.createElement('button');
                viewPlanBtn.classList.add('btn', 'btn-primary', 'me-2');
                viewPlanBtn.textContent = 'View Plan';
                viewPlanBtn.style.display = plan.plan_file ? "inline-block" : "none";

                if ( plan.payed  != 2   ) 
                {

                    viewPlanBtn.classList.add('disabled') ; 

                }
                else 
                {
                    viewPlanBtn.classList.remove('disabled') ; 

                }
                viewPlanBtn.onclick = () => {
                    if (viewPlanBtn.textContent === "View Plan") {
                        viewPlanBtn.textContent = 'Hide Plan';
                        imagePlan.style.display = "block";
                    } else {
                        viewPlanBtn.textContent = 'View Plan';
                        imagePlan.style.display = "none";
                    }
                };

                const startingDate = new Date(plan.starting_date);
                const dueDate = new Date(startingDate);
                dueDate.setDate(startingDate.getDate() + parseInt(plan.due_date));
                const currentDate = new Date();
                const remainingDays = calculateDaysDifferenceUTC(currentDate, dueDate);

                const deliverDay = document.createElement('p');
                deliverDay.innerText = `Days Remaining: ${remainingDays}`;
                deliverDay.classList.add('fw-bold');
                if (remainingDays <= 0) {
                    deliverDay.style.color = 'red';
                }
                container.appendChild(deliverDay);

                if (plan.status.toLowerCase() === 'ready') {
                    const ratingContainer = document.createElement('div');
                    ratingContainer.classList.add('mb-3');

                    for (let i = 1; i <= 5; i++) {
                        const starLink = document.createElement('a');
                        starLink.href =
                            `rating_process.php?rate=${i}&plan_id=${plan.id}&client_id=${<?php echo json_encode($client_id); ?>}&specialist_id=${plan.specialist_id}`;
                        starLink.classList.add('me-1');

                        const starImg = document.createElement('img');
                        starImg.src = plan.rate >= i ? './icons/star.png' : './icons/star_unfilled.png';
                        starImg.alt = `Star ${i}`;
                        starImg.width = 25;
                        starImg.height = 25;

                        starLink.appendChild(starImg);
                        ratingContainer.appendChild(starLink);
                    }

                    container.appendChild(ratingContainer);
                }

                const payBtn = document.createElement('button');
                payBtn.classList.add('btn', 'btn-warning', 'me-2');
                payBtn.textContent = plan.payed === 0 ?  "Pay 50%" :   "Pay 100%" ; 
                
            
                if (plan.specialist_approved == 1 && plan.status == "approved"  && plan.payed == 2) {
                    payBtn.classList.remove('disabled' ) ;  
                } else if ( plan.status == "ready" || plan.status == "approved" && plan.payed == 1 ) 
                {

                    if ( plan.plan_file_exists != 0 ) { 
                        payBtn.textContent =  "Pay 100%"  ;
                        payBtn.classList.remove('disabled') ; 
                    
                    } 
                    else   { 
                        payBtn.textContent =  "Waiting to upload the plan.."  ; 
                        payBtn.classList.add('disabled') ; 
                        
                    }
                }
                else if ( plan.status != 'approved')
                {
                    payBtn.classList.add('disabled') ; 
                    payBtn.innerText = "Waiting for approved" ; 
                }

                payBtn.onclick = () => payNow(plan.id, plan.payed);
                payBtn.style.display = plan.payed >= 0 ? 'inline-block' : 'none';
                container.appendChild(payBtn);
                container.appendChild(viewPlanBtn);
                console.log(plan.status) ; 
                console.log(`plan number ${plan.id} -- ${plan.plan_file_exists}`) ; 

                const reportBtn = document.createElement('a');
                reportBtn.innerText = "Report";
                reportBtn.href = `report.php?specialist_id=${plan.specialist_id}`;
                reportBtn.classList.add('btn', 'btn-danger');
                reportBtn.style.display = 'none';
                container.appendChild(reportBtn);
                if ( plan.payed == 2   ) {
                    payBtn.style.display = 'none' ; 
                    viewPlanBtn.style.display = "inline-block" ; 
                    if ( plan.plan_file_exists ) 
                        viewPlanBtn.classList.remove('disabled') ; 
                    else 
                        viewPlanBtn.classList.add('disabled') ; 
                    reportBtn.style.display = 'inline-block';
                    if (  plan.plan_file_exists !=  0  &&    remainingDays > 0) {
                        reportBtn.classList.add('disabled');
                        reportBtn.href = '#'; // Prevent navigation
                    } else if ( plan.plan_file_exists == 0  && remainingDays <= 0) {
                        console.log("Im here") ; 
                        reportBtn.classList.remove('disabled');
                        reportBtn.href = `report.php?specialist_id=${plan.specialist_id}`;
                    }
                } else 
                {
                    if ( plan.plan_file_exists == 0  && remainingDays <= 0) 
                        reportBtn.style.display = 'inline-block';
                    else 
                        reportBtn.style.display = 'none';


                }



                if ( plan.blocked ) 
                {   
                    var text = document.createElement('p'); 
                    text.classList.add('text-danger') ; 
                    text.innerText = `
                    
                    specialist ${plan.name} has been blocked 
                    For reporting: Ministry of Commerce: 1900 
                    | Saudi Council of Engineers: 920020820 | Technical
                     Support: 0552804238. Thank you for your 
                     cooperation
                    ` ; 
                    reportBtn.style.display = 'none' ; 
                    viewPlanBtn.style.display = 'none' ; 
                    payBtn.style.display = 'none' ;
                    container.appendChild(text);

                }

            document.getElementById('plans-container').appendChild(container);
        }

        function calculateDaysDifferenceUTC(start, end) {
            const startUTC = Date.UTC(start.getFullYear(), start.getMonth(), start.getDate());
            const endUTC = Date.UTC(end.getFullYear(), end.getMonth(), end.getDate());

            const diffInMs = endUTC - startUTC;
            const msInDay = 24 * 60 * 60 * 1000;
            const diffInDays = Math.round(diffInMs / msInDay);

            return diffInDays;
        }

        function payNow(planId, payed_num) {
            window.location.href = `Ccontract.php?planId=${planId}&payed_num=${payed_num}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (plansData.length > 0) {
                plansData.forEach(plan => {
                    updateStatusUI(plan);
                });
            } else {
                document.getElementById('plans-container').innerHTML = `
                    <p id="status-message" class="status">Status: <span id="status-text">No Plans yet</span></p>
                `;
            }
        });
    </script>
</body>

</html>
