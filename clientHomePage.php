<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Client Home Page</title>
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
        <link rel="stylesheet" href="client.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
       <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" />

        <link rel="stylesheet" href="custom.css" />

        <style>
        .star-container {
            display: flex;
            /* Arrange stars in one line */
            align-items: center;
            /* Align them vertically if needed */
        }

        .star {
            width: 20px;
            height: 20px;
            margin-right: 2px;
        }
        
        </style>
    </head>

    <body>
        <div class="navbar">
            <div class="logo" >
                <h5>Client Home(<?php echo $_SESSION['users']['client']['name'] ?>)</h5>
            </div>
            <div class="menu">
                <!-- <a href="index.html">Home</a> -->
                <a href="plan_status.php">Plan Status</a>
                <!-- <a href="userProfile.html">Profile</a> -->
                <a href="logout.php?role=client">Logout</a>
            </div>
        </div>

        <!-- Main content with Swiper -->
        <div class="container">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <!-- Specialist cards dynamically inserted from the database -->
                    <?php
                include 'connect.php';

                        // Fetching specialists from the database
              
                        $sql = "SELECT 
                        sp.id , 
                        sp.blocked, 
                        sp.name , 
                        sp.bio , 
                        sp.experience ,
                        sp.id AS specialist_id,
                        sp.name AS specialist_name,
                         COUNT(CASE WHEN r.rating > 0 THEN r.client_id END) AS total_ratings,
    COALESCE(ROUND(AVG(CASE WHEN r.rating > 0 THEN r.rating END), 1), 0) AS avg_rating 
                      FROM 
                        specialist AS sp
                      LEFT JOIN  ratings AS r ON sp.id = r.specialist_id
                      WHERE sp.blocked = 0 
                      GROUP BY  sp.id, sp.name 

                        " 
                         ;
                $result = $conn->query($sql);
                if ($result === false) {
              
                    echo "<div class='swiper-slide'><h3>Error fetching specialists: " . htmlspecialchars($conn->error) . "</h3></div>";
                } elseif ($result->num_rows > 0) {
                    
                    
                    while($row = $result->fetch_assoc()) {
                     
                        $filled_stars =    round($row['avg_rating']) ?? 0  ;
                   
                        $unfilled_stars  =  5 - $filled_stars   ;
                        
                        echo '<div class="swiper-slide">';
                        echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                        echo '<p><strong>Bio:</strong> ' . htmlspecialchars($row["bio"]) . '</p>';
                        echo '<p><strong>Experience:</strong> ' . htmlspecialchars($row["experience"]) . ' years</p>';
                        echo "<div class=star-container>" ; 

                        
                        echo "<b style='margin-right: 10px'><p>(" . htmlspecialchars($row['avg_rating'] !== null ? number_format($row['avg_rating'], 1) : "N/A") . ")</p></b>";

                        for ( $i = 0 ; $i < $filled_stars ; $i++ ) {
                            echo '<img src="./icons/star.png" alt="Star" style="display :inline-block ; width: 20px; height: 20px; margin-right: 2px;">';
                            
                        }
                        for ( $i = 0 ; $i < $unfilled_stars   ; $i++ ) {
                            echo '<img src="./icons/star_unfilled.png" alt="Star" style="width: 20px; height: 20px; margin-right: 2px;">';
                            
                        }
                        echo "<b style='margin-left  : 15px ; color : red ' ><p >  (" . htmlspecialchars($row['total_ratings'] ?? '0' ) . ")   </p></b>" ; 
                        echo "</div>" ; 
                        echo '<form action="formPage.php" method="GET">';
                        echo '<input type="hidden" name="specialist_id" value="' . $row["id"] . '">';
                        echo '<button type="submit">Request Plan</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "<div class='swiper-slide'><h3>No specialists found</h3></div>";
                }

                $conn->close();
                ?>
                </div>
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Navigation buttons -->

            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 Growing Specialists. All Rights Reserved.</p>
        </div>

        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>



        <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true
        });
        </script>
    </body>

</html>