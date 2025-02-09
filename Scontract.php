<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="Scontract.css">
        <title>Specialist Contract Agreement</title>
    </head>

    <body>

        <div class="container">
            <h1>Specialist Contract Agreement</h1>
            <p>Please review the agreement before proceeding to create the plan for the client.</p>

            <div class="terms">
                <h3>Agreement Terms:</h3>
                <ul>
                    <li>You agree to deliver the requested plan within the specified time frame.</li>
                    <li>You will provide the client with a draft of the plan for approval or changes within 3 days.</li>
                    <li>After approval, the client will pay a 50% deposit before the plan is fully executed.</li>
                    <li>You are responsible for maintaining communication with the client during the plan's creation.
                    </li>
                    <li>Failure to deliver the plan in a timely manner may result in cancellation of the contract.</li>
                </ul>
            </div>

            <label>
                <input type="checkbox" id="agree-checkbox"> I have read and agree to the terms of this agreement.
            </label>
            <div class="countdown">
                Time remaining to confirm: <span id="countdown-timer"></span>
            </div>
            <div class="button-container">
                <button class="confirm-button" id="confirm-button" disabled onclick="confirmContract()">Confirm</button>
                <button class="cancel-button" onclick="cancelContract()">Cancel</button>
            </div>
        </div>
        <script src="contract.js"></script>
        <script>
        // Function to handle contract confirmation
        function confirmContract() {
            const planId = sessionStorage.getItem('currentPlanId');
            console.log(planId);
            // Confirm the agreement and update the plan status
            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: planId,
                        action: 'approved',
                        who: 'plan'
                    })
                })
                .then(response =>  response.json())
                .then(data => {
                    console.log(data);
                    if (data.status == "approved") {
                        // alert("Contract confirmed. Proceed to the plan progress page.");
                        window.location.href = 'plan_progress.php';
                    } else {
                        alert('Error updating the status');
                    }
                });
        }

        // Function to handle contract cancellation
        function cancelContract() {
            window.location.href = 'specialistHomePage.php';
        }
        </script>

    </body>

</html>