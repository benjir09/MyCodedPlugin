<?php
foreach($products as $product) {
    $current_locations = get_the_terms($product->get_id() , 'location-country');
    $location = '';
    foreach ( $current_locations as $current_location ) {
        $location .= $current_location->name.'<span class="location-comma">,</span>';
    }
    $product_img  = $product->get_image('medium');

    ?>
    
    <div class="archive-card">
        <div class="imagewrap">
            <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product_img; ?></a>
            <span class="role">Animal</span>
        </div>
        <div class="title-wrap">
            <div class="location"><?php echo $location; ?></div>
            <h3><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></h3>
        </div>
        <div class="excerpt">
            <?php echo $product->get_short_description(); ?>
        </div>
        <div class="view-project">
            <a href="<?php echo $product->get_permalink(); ?>">View Project</a>
        </div>
    </div>

<?php }