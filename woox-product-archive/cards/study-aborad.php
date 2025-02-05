<?php
foreach($products as $product) {
    $current_locations = get_the_terms($product->get_id() , 'location-country');
    $location = '';
    foreach ( $current_locations as $current_location ) {
        $location .= $current_location->name.'<span class="location-comma">,</span>';
    }

    $current_tags = get_the_terms($product->get_id() , 'product_tag');
    $taglists = '';
    foreach ( $current_tags as $current_tag ) {
        $tag_name = $current_tag->name;
        $tag_url = get_term_link( $current_tag->term_id, 'product_tag' );
        $taglists .= '<a href="'.$tag_url.'" class="tag">'.$tag_name.'</a>';
    }
    $product_img  = $product->get_image('medium');

    ?>
    
    <div class="archive-card">
        <div class="imagewrap">
            <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product_img; ?></a>
            <span class="role">PhD</span>
        </div>
        <div class="title-wrap">
            <div class="location"><img class="cardlocationicon" src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/location.png"><?php echo $location; ?></div>
            <h3><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></h3>
        </div>
        <div class="excerpt">
            <?php echo $product->get_short_description(); ?>
        </div>
        <div class="linkntag">
            <div class="tagwrap"><?php echo $taglists; ?></div>
            <a class="studyabroadlink" href="<?php echo $product->get_permalink(); ?>">See Project <img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/arrow-right.png"></a>
        </div>
    </div>

<?php }