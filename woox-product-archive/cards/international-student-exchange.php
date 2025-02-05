<?php
foreach($products as $product) {
    $duration = get_post_meta( $product->get_id(), 'duration', true );
    $age = get_post_meta( $product->get_id(), 'age', true );
    $fee = get_post_meta( $product->get_id(), 'fee', true );
    $current_locations = get_the_terms($product->get_id() , 'location-country');
    $location = '';
    foreach ( $current_locations as $current_location ) {
        $location .= $current_location->name.'<span class="location-comma">,</span>';
    }
    $product_img  = $product->get_image('medium');

    ?>
    
    <div class="archive-card">
        <div class="title-wrap">
            <h3><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></h3>
            <div class="location"><?php echo $location; ?></div>
        </div>
        <div class="imagewrap">
            <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product_img; ?></a>
            <span class="role">Study</span>
        </div>
        <div class="exchange-extra">
            <div class="exchange-box">
                <div><img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/calender.png"></div>
                <div><?php echo $duration; ?></div>
            </div>
            <div class="exchange-box">
                <div><img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/person.png"></div>
                <div><?php echo $age; ?></div>
            </div>
            <div class="exchange-box">
                <div><img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/dollar.png"></div>
                <div><?php echo $fee; ?></div>
            </div>
        </div>
        <div class="view-project">
            <a href="<?php echo $product->get_permalink(); ?>">See Project <img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/arrow-right-black.png"></a>
        </div>
    </div>

<?php }