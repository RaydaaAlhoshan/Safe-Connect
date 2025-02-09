<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Status</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
            <div class="text-center" id="spinnerContainer">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Processing your payment...</p>
            </div>

            <div class="text-center mt-5" id="paymentMessage" style="display: none;">
                <h3 class="text-success">Payment Successful!</h3>
                <p>Your payment has been completed. Thank you for your purchase.</p>
                <a class="btn btn-outline-dark" href="plan_status.php">Go back</a>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        setTimeout(() => {
            document.getElementById('spinnerContainer').style.display = 'none';
            document.getElementById('paymentMessage').style.display = 'block';
        }, 3000);
        </script>
    </body>

</html>