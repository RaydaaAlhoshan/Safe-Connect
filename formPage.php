<?php
session_start();

 



?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request Plan - Form Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="formPage.css">
    </head>

    <body>
        <div class="container mt-5">
            <h1 class="mb-4">Request Plan Form</h1>
            <form id="request-form" action="Ccontract.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="specialist_id"
                    value="<?php echo htmlspecialchars($_GET['specialist_id']); ?>">
                <input type="hidden" name="client_id" value="<?php echo $_SESSION['users']['client']['id']; ?>">

                <div class="mb-3">
                    <label for="client-name" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="client-name" name="name"
                        value="<?php echo $_SESSION['users']['client']['name']; ?>" placeholder="Enter your name" required>
                </div>

                <div class="mb-3">
                    <label for="place" class="form-label">Select Place</label>
                    <select class="form-select" id="place" name="place" required>
                        <option value="">Select a place</option>
                        <option value="factory">Factory</option>
                        <option value="school">School</option>
                        <option value="office">Office</option>
                        <option value="shop">Shop</option>
                        <option value="gym">Gym</option>
                        <option value="hospital">Hospital</option>
                        <option value="university">University</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Measures</label>
                    <div class="d-flex">
                        <div class="me-3">
                            <label for="height" class="form-label">Height</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="height" id="height">
                                <span class="input-group-text">cm</span>
                            </div>
                        </div>
                        <div>
                            <label for="width" class="form-label">Width</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="width" id="width">
                                <span class="input-group-text">cm</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="price-range" class="form-label">Price Range</label>
                    <div class="d-flex justify-content-between">
                        <span>0</span>
                        <span>10000</span>
                    </div>
                    <input type="range" class="form-range" value="1" step="0.5" max="10000" min="0" name="price_range"
                        id="price-range">
                    <output id="price-output">0.0</output>
                </div>

                <div class="mb-3">
                    <label for="due-date" class="form-label">Due Date</label>
                    <select class="form-select" id="due-date" name="due_date" required>
                        <option value="7">1 week</option>
                        <option value="21">3 weeks</option>
                        <option value="42">6 weeks</option>
                        <option value="other">Other</option>
                    </select>
                    <input type='number' class="form-control mt-2" id='custom_input' name="custom_due_date"
                        style='display: none' placeholder="Enter number of days">
                </div>

                <div class="mb-3">
                    <label for="comments" class="form-label">Additional Comments</label>
                    <textarea class="form-control" id="comments" name="comments" rows="4"
                        placeholder="Enter any additional comments here"></textarea>
                </div>

                <div class="mb-3">
                    <label for="blueprint" class="form-label">Upload Blueprint</label>
                    <input type="file" class="form-control" id="blueprint" name="blueprint"
                        accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.gif" required>
                </div>

                <button type="submit" class="btn" style="background-color : #ff5555;">Next</button>
            </form>

            <hr class="my-5">

       
     

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due-date').setAttribute('min', today);
        var price_range = document.getElementById("price-range");
        var price_output = document.getElementById("price-output");
        price_range.addEventListener("input", () => {
            price_output.innerText = price_range.value;
        });

        var dropdown = document.getElementById('due-date');
        var custom_input = document.getElementById('custom_input');
        dropdown.addEventListener('change', () => {
            if (dropdown.value !== "other") {
                custom_input.style.display = "none";
            } else {
                custom_input.style.display = "block";
            }
        });

        document.getElementById('displayBlueprintBtn1').addEventListener('click', function() {
            const planId = document.getElementById('planIdInput').value;
            if (!planId) {
                alert('Please enter a valid Plan ID.');
                return;
            }
            fetch('blueprints.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${planId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const blueprintContainer = document.getElementById('blueprintContainer1');
                        blueprintContainer.innerHTML =
                            `<img src="uploads/${data.blueprint}" alt="Blueprint" class="img-fluid" />`;
                    } else {
                        alert(data.message || 'Error retrieving blueprint.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_plans.php')
                .then(response => response.json())
                .then(data => {
                    const dropdown = document.getElementById('planDropdown');
                    dropdown.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(plan => {
                            const option = document.createElement('option');
                            option.value = plan.id;
                            option.textContent = `Plan ${plan.id} - ${plan.place}`;
                            dropdown.appendChild(option);
                        });
                    } else {
                        dropdown.innerHTML = '<option value="">No plans available</option>';
                    }
                })
                .catch(error => console.error('Error fetching plans:', error));
        });

        document.getElementById('displayBlueprintBtn2').addEventListener('click', function() {
            const planId = document.getElementById('planDropdown').value;
            if (!planId) {
                alert('Please select a plan.');
                return;
            }
            fetch('fetch_plans.php')
                .then(response => response.json())
                .then(data => {
                    const selectedPlan = data.find(plan => plan.id == planId);
                    if (selectedPlan) {
                        const blueprintContainer = document.getElementById('blueprintContainer2');
                        blueprintContainer.innerHTML =
                            `<img src="uploads/${selectedPlan.blueprint}" alt="Blueprint" class="img-fluid" />`;
                    } else {
                        alert('Blueprint not found for the selected plan.');
                    }
                })
                .catch(error => console.error('Error fetching blueprint:', error));
        });
        </script>
    </body>

</html>