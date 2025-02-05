<?php
    foreach($products as $product) {
        $current_locations = get_the_terms($product->get_id() , 'location-country');
        $location = '';
        foreach ( $current_locations as $current_location ) {
            $location .= $current_location->name.'<span class="location-comma">, </span>';
        }
        $product_img  = $product->get_image('medium');


        $archive_card .= '<div class="archive-card">
                            <div class="imagewrap">
                                <a href="'.$product->get_permalink().'">'.$product_img.'</a>
                                <span class="role">Animal</span>
                            </div>
                            <div class="title-wrap">
                                <div class="location">'.$location.'</div>
                                <h3><a href="'.$product->get_permalink().'">'.$product->get_title().'</a></h3>
                            </div>
                            <div class="excerpt">'.$product->get_short_description().'</div>
                            <div class="view-project"><a href="'.$product->get_permalink().'">View Project</a></div>
                        </div>';
    }