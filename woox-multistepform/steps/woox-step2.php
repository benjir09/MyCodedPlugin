<?php
    if ( $passed_product_id ) {
        $passed_product_obj = wc_get_product($passed_product_id);
        $passed_product_type = $passed_product_obj->get_type();

        $program_details_top = self::all_product_meta( $passed_product_id, $passed_product_type, $passed_product_obj);
    }
?>

<div id="msform-step2" class="msform-step2 msform-step">

    <div class="msform-steps-header">
        <h4 class="h4">02 Program</h4>
        <h2 class="h2">Program <span>Details</span></h2>
        <h5 class="h5">Applying for your dream program is quick and simple.</h5>
    </div>

    <div class="msform-step2body msformbody">
        <div class="program-details-top-wrap">
            <?php //echo $program_details_top; ?>
        </div>

        <div class="program-details-form">
            <div class="two-col-input">
                <div class="program-details-form-input pdfi50">
                    <label for="expected-salary">Expected Salary</label>
                    <input type="text" id="expected-salary" name="expected_salary" required>
                </div>
                <div class="program-details-form-input pdfi50">
                    <label for="last-salary">Last Salary</label>
                    <input type="text" id="last-salary" name="last_salary" required>
                </div>
            </div>
            <div class="program-details-form-input pdfi100">
                <label for="previous-company">Previous Company</label>
                <input type="text" id="previous-company" name="previous_company" required>
            </div>
            <div class="two-col-input">
                <div class="program-details-form-input pdfi50">
                    <label for="experience">Experience</label>
                    <input type="text" id="experience" name="experience" required>
                </div>
                <div class="program-details-form-input pdfi50">
                    <label for="how-soon-join">How soon you can join?</label>
                    <input type="text" id="how-soon-join" name="how_soon_join" required>
                </div>
            </div>
            <div class="program-details-form-input pdfi100 resumewrap">
                <input type="file" accept=".jpg,.png,.doc,.docx,.pdf" id="resume" name="resume">
                <label for="resume">Upload your Resume Here</label>
                <div class="uploadedfile"></div>
            </div>
        </div>

    </div>
</div>