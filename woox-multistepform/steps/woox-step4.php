<div id="msform-step4" class="msform-step4 msform-step">

    <div class="msform-steps-header">
        <h4 class="h4">04 Payment Details</h4>
        <h2 class="h2">Payment <span>Details</span></h2>
        <h5 class="h5">Applying for your dream program is quick and simple.</h5>
    </div>

    <div class="msform-step4body msformbody">

        <div class="program-details-form payment-details-form">

            <div class="payment-type">

            <?php
                            
                $paymentGateways = WC()->payment_gateways->get_available_payment_gateways();
                $i=0;
                foreach($paymentGateways as $gateway) {
                    // if($gateway->id==='cod'){ // 'cpsw_stripe'
                    //echo'<div><label class="input-label"><input type="radio" id="payment_method_'.$gateway->id.'" value="'.$gateway->id.'" name="payment_method" '.($i==0?'checked':'').' data-title="'.__($gateway->title,'woocommerce').'"> '.$gateway->title.'</label></div>';

                    echo '<div class="each-payment">
                        <input type="radio" alt='.$gateway->title.' id="gateway_'.$gateway->id.'" name="payment_method" value="'.$gateway->id.'" '.($i==0?'checked':'').'>
                        <label for="gateway_'.$gateway->id.'">'.$gateway->title.'</label>
                    </div>';
                    
                    if ( $gateway->has_fields()){
                        echo $gateway->payment_fields();
                    }
                    $i++;
                }
            
            ?>
                <!-- <div class="each-payment">
                    <input type="radio" id="credit-debit" name="payment_type" value="credit-debit">
                    <label for="credit-debit">Credit/Debit Card</label>
                </div>
                <div class="each-payment">
                    <input type="radio" id="paypal" name="payment_type" value="paypal">
                    <label for="paypal">Paypal</label>
                </div>
                <div class="each-payment">
                    <input type="radio" id="bank-ransfer" name="payment_type" value="bank-ransfer">
                    <label for="bank-ransfer">Bank Transfer</label>
                </div> -->
            </div>

            <div class="personal-details-form-input">
                <label for="name-on-card">Name on Card</label>
                <input type="text" id="name-on-card" name="name_on_card">
            </div>
            <div class="personal-details-form-input">
                <label for="card-number">Card Number</label>
                <input type="text" id="card-number" name="card_number">
            </div>

            <div class="two-col-input">
                <div class="program-details-form-input pdfi50">
                    <label for="expiration-date">Expiration Date</label>
                    <input type="date" id="expiration-date" name="expiration_date">
                </div>
                <div class="program-details-form-input pdfi50">
                    <label for="security-code">Security Code (CVV)</label>
                    <input type="number" id="security-code" name="security_code">
                </div>
            </div>

            <p class="payment-notice">For your security, your bank may send a code via your banking app, email or text to confirm payment.</p>
        </div>

        <div class="payment-details-card">
            <div class="pdc-wrap">
                <h5 class="pdc-title">Total Fees</h5>
                <h4 class="pdc-total">$0</h4>
                <hr>
                <div class="pdc-details">
                    <table>
                        <tr>
                            <td>$</td>
                            <td>Destination</td>
                            <td>Botswana</td>
                        </tr>
                        <tr>
                            <td>$</td>
                            <td>Project</td>
                            <td id="project-final">Refugee Volunteer</td>
                        </tr>
                        <tr>
                            <td>$</td>
                            <td>Cost</td>
                            <td id="pdc-total-td">$0</td>
                        </tr>
                        <tr>
                            <td>$</td>
                            <td>Start</td>
                            <td>17<span class="th">th</span>Octber 2024</td>
                        </tr>
                        <tr>
                            <td>$</td>
                            <td>End</td>
                            <td>30<span class="th">th</span>Octber 2024</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>