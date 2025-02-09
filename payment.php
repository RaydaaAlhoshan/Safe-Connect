<?php
session_start();
require 'connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Plan Page</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="payment.css">
    </head>

    <body>
<?php



    $plan_id      = $_GET['planId']; 
    $price        = $_GET['price'];
    $bankacc      = $_GET['bankacc'];
    $currentAmount= $_GET['currentAmount'];
    $payment_num  = $_GET['payment_num']; 
    $client_id    = $_SESSION['users']['client']['id'];
    $required_fields = ['planId'   , 'price' , 'bankacc' ,  'currentAmount'  , 'payment_num'  ] ; 
    // foreach ($required_fields as $field) {
    //     if ( empty($_GET[$field])  && field  ) {
   
    //         echo json_encode(['success' => false, 'message' => 'Missing required fields.  ' .$field ]);
    //         exit();
    //     }
    // }

    // var_dump($_GET) ; 
    // exit () ; 
    $sql  = "SELECT payment.*, client.name 
             FROM payment
             INNER JOIN client ON client.id = payment.client_id
             WHERE client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $payment_data = $stmt->get_result()->fetch_assoc();

        $amount       = $payment_data['amount'] ;
        $cvv          = $payment_data['cvv'] ;
        $expiry_date  = $payment_data['expiry_date']  ;
        $card_number  = $payment_data['account_number'];
        $client_name  = $payment_data['name'];
?>

        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-lg-0 mb-3">
                    <div class="card p-3">
                        <div class="img-box">
                            <img src="https://www.freepnglogos.com/uploads/visa-logo-download-png-21.png" alt="Visa">
                        </div>
                        <div class="number">
                            <label class="fw-bold">**** **** **** 1060</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <small><span class="fw-bold">Expiry date:</span><span>10/16</span></small>
                            <small><span class="fw-bold">Name:</span><span>Kumar</span></small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-lg-0 mb-3">
                    <div class="card p-3">
                        <div class="img-box">
                            <img src="https://www.freepnglogos.com/uploads/mastercard-png/file-mastercard-logo-svg-wikimedia-commons-4.png"
                                alt="MasterCard">
                        </div>
                        <div class="number">
                            <label class="fw-bold">**** **** **** 1060</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <small><span class="fw-bold">Expiry date:</span><span>10/16</span></small>
                            <small><span class="fw-bold">Name:</span><span>Kumar</span></small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-lg-0 mb-3">
                    <div class="card p-3">
                        <div class="img-box">
                            <img src="https://www.freepnglogos.com/uploads/discover-png-logo/credit-cards-discover-png-logo-4.png"
                                alt="Discover">
                        </div>
                        <div class="number">
                            <label class="fw-bold">**** **** **** 1060</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <small><span class="fw-bold">Expiry date:</span><span>10/16</span></small>
                            <small><span class="fw-bold">Name:</span><span>Kumar</span></small>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="card p-3">
                        <p class="mb-0 fw-bold h4">Payment Methods</p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card p-3">
                        <!-- PayPal collapsible -->
                        <div class="card-body border p-0">
                            <p>
                                <a class="btn btn-primary w-100 d-flex align-items-center justify-content-between"
                                    data-bs-toggle="collapse" href="#collapsePaypal" role="button" aria-expanded="true"
                                    aria-controls="collapsePaypal">
                                    <span class="fw-bold">PayPal</span>
                                    <span class="fab fa-cc-paypal"></span>
                                </a>
                            </p>
                            <div class="collapse p-3 pt-0" id="collapsePaypal">
                                <div class="row">
                                    <div class="col-8">
                                        <p class="h4 mb-0">Summary</p>
                                        <p class="mb-0">
                                            <span class="fw-bold">Product:</span>
                                            <span class="c-green">Name of product</span>
                                        </p>
                                        <p class="mb-0">
                                            <span class="fw-bold">Price:</span>
                                            <span class="c-green">$<?php echo htmlspecialchars($price ?? 0 ); ?></span>
                                        </p>
                                        <p class="mb-0">
                                            Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                                            Atque nihil neque quisquam aut repellendus, dicta vero?
                                            Animi dicta cupiditate, facilis provident quibusdam ab quis,
                                            iste harum ipsum hic, nemo qui!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body border p-0">
                            <p>
                                <a class="btn btn-primary p-2 w-100 d-flex align-items-center justify-content-between"
                                    data-bs-toggle="collapse" href="#collapseCreditCard" role="button"
                                    aria-expanded="true" aria-controls="collapseCreditCard">
                                    <span class="fw-bold">Credit Card</span>
                                    <span>
                                        <span class="fab fa-cc-amex"></span>
                                        <span class="fab fa-cc-mastercard"></span>
                                        <span class="fab fa-cc-discover"></span>
                                    </span>
                                </a>
                            </p>
                            <div class="collapse show p-3 pt-0" id="collapseCreditCard">
                                <div class="row">
                                    <!-- Summary Column -->
                                    <div class="col-lg-5 mb-lg-0 mb-3">
                                        <p class="h4 mb-0">Summary</p>
                                        <p class="mb-0">
                                            <span class="fw-bold">Plan ID:</span>
                                            <span class="c-green"><?php echo htmlspecialchars($plan_id); ?></span>
                                        </p>
                                        <p class="mb-0">
                                            <span class="fw-bold">Total price:</span>
                                            <span class="c-green">$<?php echo htmlspecialchars($price); ?></span>
                                        </p>
                                        <!-- <p class="mb-0">
                                            <span class="fw-bold">Available balance:</span>
                                            <span class="c-green"><?php echo htmlspecialchars($amount); ?></span>
                                        </p> -->

                                        <?php

                            if ( $payment_num == '0' )
                                $toPay = $price / 2;
                            else if ( $payment_num == '1' )  
                                $toPay = $price ; 
                            else 
                                $toPay = "Nan" ; 


                              
                                ?>

                                  <?php if ( $payment_num == '1' ) :?> 
                                        <p class="mb-0">
                                            <span class="fw-bold">Amount to pay (100%):</span>
                                            <span class="text-danger">-$<?php echo htmlspecialchars($toPay); ?></span>
                                        </p>
                                    </div>
                                    <?php else : ?> 
                                        <p class="mb-0">
                                            <span class="fw-bold">Amount to pay (50%):</span>
                                            <span class="text-danger">-$<?php echo htmlspecialchars($toPay); ?></span>
                                        </p>
                                    </div>

                                    <?php endif ?>  
                                    <!-- Form Column -->
                                    <div class="col-lg-7">
                                        <form method="POST" action="payment_process.php">
                                            <div class="row">
                                                <!-- Card Number -->
                                                <div class="col-12">
                                                    <div class="form__div mb-3">
                                                        <label class="form-label fw-bold">Card Number</label>
                                                        <input type="text" name="card_num" class="form-control"
                                                            placeholder=" "
                                                            value="<?php echo htmlspecialchars($card_number ?? ''); ?>">
                                                    </div>
                                                </div>

                                                <!-- Expiry Date -->
                                                <div class="col-6">
                                                    <div class="form__div mb-3">
                                                        <label class="form-label fw-bold">MM/YY</label>
                                                        <input type="text" name="expiry_date" class="form-control"
                                                            placeholder=" "
                                                            value="<?php echo htmlspecialchars($expiry_date ?? '' ); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form__div mb-3">
                                                        <label class="form-label fw-bold">CVV</label>
                                                        <input type="password" name="cvv" class="form-control"
                                                            placeholder=" "
                                                            value="<?php echo htmlspecialchars($cvv ?? '' ); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form__div mb-3">
                                                        <label class="form-label fw-bold">Cardholder Name</label>
                                                        <input type="text" name="cardholder_name" class="form-control"
                                                            value="<?php echo htmlspecialchars($client_name ?? ''); ?>">
                                                    </div>
                                                </div>

                                                <input type="hidden" name="payment_num"
                                                    value="<?php echo htmlspecialchars($payment_num); ?>">
                                                <input type="hidden" name="plan_id"
                                                    value="<?php echo htmlspecialchars($plan_id); ?>">
                                                <input type="hidden" name="specialist_bankacc"
                                                    value="<?php echo htmlspecialchars($bankacc); ?>">
                                                <input type="hidden" name="price"
                                                    value="<?php echo htmlspecialchars($toPay); ?>">

                                                <div class="col-12 mt-3">
                                                    <?php if ($amount >= $toPay) : ?>
                                                    <button type="submit" class="btn btn-primary w-100">Make
                                                        Payment</button>
                                                    <?php else : ?>
                                                    <button type="submit" disabled class="btn btn-danger w-100">
                                                        You don't have enough balance
                                                    </button>
                                                    <?php endif; ?>
                                                </div> 
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
        </script>
    </body>

</html>