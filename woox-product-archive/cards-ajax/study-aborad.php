<?php
    foreach($products as $product) {
        $current_locations = get_the_terms($product->get_id() , 'location-country');
        $location = '';
        foreach ( $current_locations as $current_location ) {
            $location .= $current_location->name.'<span class="location-comma">, </span>';
        }

        $current_tags = get_the_terms($product->get_id() , 'product_tag');
        $taglists = '';
        foreach ( $current_tags as $current_tag ) {
            $tag_name = $current_tag->name;
            $tag_url = get_term_link( $current_tag->term_id, 'product_tag' );
            $taglists .= '<a href="'.$tag_url.'" class="tag">'.$tag_name.'</a>';
        }

        $product_img  = $product->get_image('medium');


        $archive_card .= '<div class="archive-card">
                            <div class="imagewrap">
                                <a href="'.$product->get_permalink().'">'.$product_img.'</a>
                                <span class="role">PhD</span>
                            </div>
                            <div class="title-wrap">
                                <div class="location"><img class="cardlocationicon" src="'.plugin_dir_url( dirname(__FILE__) ).'images/location.png">'.$location.'</div>
                                <h3><a href="'.$product->get_permalink().'">'.$product->get_title().'</a></h3>
                            </div>
                            <div class="excerpt">'.$product->get_short_description().'</div>
                            <div class="linkntag">
                                <div class="tagwrap">'.$taglists.'</div>
                                <a class="studyabroadlink" href="'.$product->get_permalink().'">See Project <img src="'.plugin_dir_url( dirname(__FILE__) ).'images/arrow-right.png"></a>
                            </div>
                        </div>';
    }