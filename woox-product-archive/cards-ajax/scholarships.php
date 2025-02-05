<?php
    foreach($products as $product) {
        $current_tags = get_the_terms($product->get_id() , 'product_tag');

        $current_locations = get_the_terms($product->get_id() , 'location-country');
        $location = '';
        foreach ( $current_locations as $current_location ) {
            $location .= $current_location->name.'<span class="location-comma">,</span>';
        }
        $scholarships_percentage = get_post_meta( $product->get_id(), 'scholarships_percentage', true );

        $created_date = $product->get_date_created();
        $current_date = date('Y-m-d h:i:s');
        $diff_date = abs(strtotime($created_date) - strtotime($current_date));

        $years = floor($diff_date / (365*60*60*24));
        $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        $product_img  = $product->get_image('medium');

        if ( ! $years ) {
            if ( ! $months ) {
                if ( ! $days ) {
                    $final_difference = 'Error';
                } else {
                    $final_difference = 'Posted '.$days.' days ago';
                }
            } else {
                $final_difference = 'Posted '.$months.' month ago';
            }
        } else {
            $final_difference = 'Posted '.$years.' years ago';
        }

        $taglist = '';
        foreach( $current_tags as $current_tag ) {
            $tag_name = $current_tag->name;
            $tag_url = get_term_link( $current_tag->term_id, 'product_tag' );
            $taglist .= '<a href="'.$tag_url.'" class="tag">'.$tag_name.'</a>';
        }


        $archive_card .= '<div class="archive-card">
                            <div class="imagewrap">
                                <a href="'.$product->get_permalink().'">'.$product_img.'</a>
                                <span class="role">Masters</span>
                            </div>
                            <div class="title-wrap">
                                <h3><a href="'.$product->get_permalink().'">'.$product->get_title().'</a></h3>
                                <div class="price">$'.$scholarships_percentage.'/<span> Scholarship</span></div>
                            </div>
                            <div class="metawrap">
                                <div class="meta"><img src="'.plugin_dir_url( dirname( __FILE__) ).'images/location.png">'.$location.'</div>
                                <div class="meta"><img src="'.plugin_dir_url( dirname( __FILE__ ) ).'images/clock.png">'.$final_difference.'</div>
                            </div>
                            <div class="tagwrap">'.$taglist.'</div>
                            <div class="excerpt">'.$product->get_short_description().'</div>
                        </div>';
    }