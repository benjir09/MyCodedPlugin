<?php
    foreach($products as $product) {
        $duration = get_post_meta( $product->get_id(), 'duration', true );
        $age = get_post_meta( $product->get_id(), 'age', true );
        $fee = get_post_meta( $product->get_id(), 'fee', true );
        $current_locations = get_the_terms($product->get_id() , 'location-country');
        $location = '';
        foreach ( $current_locations as $current_location ) {
            $location .= $current_location->name.'<span class="location-comma">, </span>';
        }
        $product_img  = $product->get_image('medium');


        $archive_card .= '<div class="archive-card">
                            <div class="title-wrap">
                                <h3><a href="'.$product->get_permalink().'">'.$product->get_title().'</a></h3>
                                <div class="location">'.$location.'</div>
                            </div>
                            <div class="imagewrap">
                                <a href="'.$product->get_permalink().'">'.$product_img.'</a>
                                <span class="role">Study</span>
                            </div>
                            <div class="exchange-extra">
                                <div class="exchange-box">
                                    <div><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/calender.png"></div>
                                    <div>'.$duration.'</div>
                                </div>
                                <div class="exchange-box">
                                    <div><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/person.png"></div>
                                    <div>'.$age.'</div>
                                </div>
                                <div class="exchange-box">
                                    <div><img src="'.plugin_dir_url( dirname(__FILE__) ).'images/dollar.png"></div>
                                    <div>'.$fee.'</div>
                                </div>
                            </div>
                            <div class="view-project"><a href="'.$product->get_permalink().'">See Project <img src="'.plugin_dir_url( dirname(__FILE__) ).'images/arrow-right-black.png"></a></div>
                        </div>';
    }