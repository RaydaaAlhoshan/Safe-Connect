<?php
session_start();
require 'connect.php';

// Assuming the specialist ID is stored in the session
$specialist_id = $_SESSION['users']['specialist']['id'] ; 

$status_message = "Please wait for certificate approval.";

$query = "SELECT  *  FROM specialist WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $specialist_id);
$stmt->execute();
$result = $stmt->get_result();
$re = $result->fetch_assoc() ;
$is_blocked = $re['blocked'] ; 



?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Specialist Home Page</title>
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
        <link rel="stylesheet" href="specialist.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" />
        <link rel="stylesheet" href="custom.css" />

    </head>

    <body>

        <header>
            Specialist Dashboard (<?php echo htmlspecialchars($_SESSION['users']['specialist']['name'] ); ?>)
            <h3>
                <a href="plan_progress.php">Plan Progress</a>
                <a href="logout.php?role=specialist">Logout</a>
            </h3>
        </header>


        <?php if($is_blocked) :?>
            <div class="container"> 
                    <div class="card card-body">
                          <img class="card-img-top" src="icons/ad.png" alt="Card image cap">
                            <h2 class="text-danger"> Your Account has been blocked From our System </h2>

                    </div>

            </div>


        <?php else :?> 
<div class="container">
            <div class="swiper-container">
                <div class="swiper-wrapper" id="plan-container">
                </div>

                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <?php endif ?> 
        
        <footer>
            © 2024 Specialist Dashboard | All Rights Reserved
        </footer>

        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script>
        fetch('fetch_plans.php')
            .then(response => response.json())
            .then(plans => {
                const container = document.getElementById('plan-container');
                console.log(plans) ; 
                if (plans.length === 0) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.classList.add('swiper-slide');
                    emptyMessage.innerHTML = `
                        <div class="card">
                            <h2>No plans Submitted</h2>
                            <p>There are no plans available at the moment.</p>
                        </div>
                    `;
                    container.appendChild(emptyMessage);
                } else {

                    plans.forEach(plan => {
                        const slide = document.createElement('div');
                        slide.classList.add('swiper-slide');

                        console.log(plan.price) ; 
                        slide.innerHTML = `
                            <div class="card">
                                <h2>${plan.place}</h2>
                                <p class="due-date">Due Date: ${plan.due_date}</p>
                                <div class='row'> 
                                <p class='col-md-6'> width : ${plan.width}   height : ${plan.height} </p>
                                <p class="">price : ${plan.price}</p>

                                </div>
                                <p> comment : ${plan.comments ? plan.comments : "No additional comments"}</p>
                                    <img  src="blueprints.php?file=${encodeURIComponent(plan.blueprint)}" style="display : none ; width : 100% "  id=blueprintImg_${plan.id} alt="Blueprint Image">
                                    <button class="download" onclick="((e)=> { 
                                            const imgElement = document.getElementById('blueprintImg_${plan.id}');
                                            if (imgElement.style.display == 'block')     
                                                imgElement.style.display = 'none';
                                            else  
                                                imgElement.style.display = 'block';


                                         

                                    } )()"  >Display Blueprint</button>                                <div>
                                     <p> 
                                     
                                     
                         
                                     
                                     
                                     </p>
                                    <button class="decline" onclick="declinePlan(${plan.id})">Decline</button>
                                    <button class="provide-plan" onclick="providePlan(${plan.id})">Aceept </button>
                                </div>
                            </div>
                        `;

                        if (plan.blocked == 0 )
                            container.appendChild(slide);
                    });
                }
            });

        // Initialize Swiper after cards are loaded

        new Swiper('.swiper-container', {
            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            centeredSlides: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },

            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            centeredSlides: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        function providePlan(planId) {
            sessionStorage.setItem('currentPlanId', planId);
            window.location.href = 'Scontract.php';
        }

        function declinePlan(planId) {
            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        planId: planId,
                        status: 'declined',
                        who: 'plan'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Plan has been declined successfully.');
                        window.location.reload();
                    } else {
                        alert('Error updating the status');
                    }
                });
        }
        </script>
    </body>

</html>