<?php
    foreach($products as $product) {
        $current_tags = get_the_terms($product->get_id() , 'product_tag');
        $employment_status = get_post_meta( $product->get_id(), 'employment_status', true );
        if ( ! $employment_status ) $employment_status = 'Not set';
        // echo $status;
        $location = get_post_meta( $product->get_id(), 'location', true );
        if ( ! $location ) $location = 'Not set';
        $created_date = $product->get_date_created();
        $current_date = date('Y-m-d h:i:s');
        $diff_date = abs(strtotime($created_date) - strtotime($current_date));

        $years = floor($diff_date / (365*60*60*24));
        $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

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
                                <a href=""><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/archive.jpg"></a>
                                <span class="role">'.$employment_status.'</span>
                            </div>
                            <div class="title-wrap">
                                <h3><a href="'.$product->get_permalink().'">'.$product->get_title().'</a></h3>
                                <div class="price">$'.$product->get_price().'/<span>weekly</span></div>
                            </div>
                            <div class="metawrap">
                                <div class="meta"><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/location.png">'.$location.'</div>
                                <div class="meta"><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/clock.png">'.$final_difference.'</div>
                            </div>
                            <div class="tagwrap">'.$taglist.'</div>
                            <div class="excerpt">'.$product->get_short_description().'</div>
                        </div>';
    }