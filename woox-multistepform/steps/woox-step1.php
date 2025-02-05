<?php
    $passed_product_id = $_GET['product_id'];
    $passed_category_id = $_GET['category_id'];

    if ($passed_product_id && $passed_category_id) {
        $from_product = true;
        $prod_args = array (
            'limit' => -1,
            'tax_query' => array(
                array (
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'     =>  $passed_category_id,
                    'operator'  => 'IN'
                ),
            ),
        );
        $selected_products = wc_get_products($prod_args);
    }
?>


<div id="msform-step1" class="msform-step1 msform-step activestep">
    <div class="msform-steps-header">
        <h4 class="h4">01 Program</h4>
        <h2 class="h2">Select <span>Program</span></h2>
        <h5 class="h5">Applying for your dream program is quick and simple.</h5>
    </div>
    <div class="msform-step1body msformbody">

        <?php 
            $args = array(
                'taxonomy'      => 'product_cat',
                'hide_empty'    => false,
                'parent'        => 0,
                'exclude'       => 24
            );

            $parent_categories = get_categories( $args );
            foreach ($parent_categories as $parent_category) {
                $has_checked = ($parent_category->term_id ==  $passed_category_id) ? 'checked' : '';
                ?>
                <div class="program-radioninput">
                    <div class="radioinputwrap">
                        <label for="<?php echo $parent_category->slug ?>"><?php echo $parent_category->name ?></label>
                        <input type="radio" id="<?php echo $parent_category->slug ?>" name="program_category" alt="<?php echo $parent_category->name ?>" value="<?php echo $parent_category->term_id ?>" <?php echo $has_checked;?>>
                    </div>
                </div>
        <?php } ?>
    </div>

    <div class="selectproduct">
        <label for="program-product">Select Product from <span id="prod_cat">bellow</span></label>
        <select name="program_product" id="program-product">
            <?php 
                if( $from_product ) {
                    foreach($selected_products as $selected_product) {
                        $has_selected = ($selected_product->get_id() ==  $passed_product_id) ? 'selected' : null;
                        echo '<option value='.$selected_product->get_id().' '.$has_selected.'>'.$selected_product->get_title().'</option>';
                    }
                } else {
                    echo '<option value="">-----</option>';
                }
            ?>
        </select>
        <div class="ajaxloading"></div>
    </div>
</div>