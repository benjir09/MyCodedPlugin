<?php 
/*
Plugin Name: Woox Products Archive
Plugin URI: #
Description: Products Archieve based on Category & Style.. short code is [archiveproducts]
Version: 1.0
Author: WooXperto LLC
Author URI: https://www.wooxperto.com/
*/

if ( ! defined( 'ABSPATH' ) ){
    exit;
}

class Woox_Archive_Products {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'ap_form_css_js']);
        add_shortcode( 'archiveproducts', [$this, 'archive_products'] );
        add_shortcode( 'destinations', [$this, 'destinations'] );
        add_shortcode( 'giveaways', [$this, 'giveaways'] );
        add_shortcode( 'campaign', [$this, 'campaign'] );
        add_action('init', [$this, 'other_func']);
    }

    public function other_func() {
        add_action('wp_ajax_archive_loadmore', [$this, 'archive_loadmore']);
        add_action('wp_ajax_nopriv_archive_loadmore', [$this, 'archive_loadmore']);
        add_action('wp_ajax_country_loadmore', [$this, 'country_loadmore']);
        add_action('wp_ajax_nopriv_country_loadmore', [$this, 'country_loadmore']);
        add_action('wp_ajax_giveway_loadmore', [$this, 'giveway_loadmore']);
        add_action('wp_ajax_nopriv_giveway_loadmore', [$this, 'giveway_loadmore']);
        add_action('wp_ajax_campaign_loadmore', [$this, 'campaign_loadmore']);
        add_action('wp_ajax_nopriv_campaign_loadmore', [$this, 'campaign_loadmore']);
    }

    public function ap_form_css_js() {
        wp_enqueue_style( 'apcss', plugins_url( 'woox-product-archive.css', __FILE__ ) );
        wp_enqueue_script( 'apjs', plugins_url( 'woox-product-archive.js', __FILE__ ), array( 'jquery' ) );
    }

    public function destinations() {
        ob_start();
        ?>
        
        <div class="destination-archive">
            <div class="destination-archive-wrap">
                <?php 
                    $args = array(
                        'taxonomy'      => 'location-country',
                        'hide_empty'    => false,
                        'orderby'       => 'name',
			            'order'         => 'ASC',
                        'number'        => 3
                    );
                    $all_locations = get_categories( $args );
                    foreach ($all_locations as $location) {
                        $term_link = get_term_link($location->term_id);
                        $location_imgid = get_term_meta( $location->term_id, 'location_background_image' );
                        $location_img = wp_get_attachment_image($location_imgid[0], 'medium');
                        // $this->dump($term_link);
                        ?>
                        <a class="location-card" href="<?php echo esc_url($term_link); ?>">
                        <?php 
                        if ( $location_img ) echo $location_img;
                        else { echo '<img src="'.esc_url( plugins_url( 'images/country.jpg', __FILE__ ) ).'">'; } 
                        ?>
                            <h4><img class="icon" src="<?php echo plugin_dir_url( __FILE__ ) ?>images/location.png"><?php echo $location->name; ?></h4>
                        </a>
                    <?php } ?>
            </div>
            <?php if (count(get_categories(['taxonomy' => 'location-country', 'hide_empty' => false])) > 3) { ?>
                <div class="archive-more">
                    <a href="#" id="country-morelink" class="archive-morelink">See more <img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/icon-more.png"></a>
                    <div class="country-ajaxmore"></div>
                </div>
            <?php } ?>
        </div>

    <?php
        return ob_get_clean();
    }
    
    public function archive_products($atts) {
        $atts = shortcode_atts(
            array(
                'id' => null,
                'style' => 'default'
            ),
            $atts
        );
        ob_start();
        ?>
        
        <div class="woox-archive">
            <?php
            $category_id = $atts['id'];
            $shortcode_style = $atts['style'];
            $prod_args = array (
                'limit' => 3,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => array(
                    array (
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'     =>  $category_id,
                        'operator'  => 'IN'
                    ),
                ),
            );
            $products = wc_get_products($prod_args);
            $products_count = count(wc_get_products(['limit' => -1, 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $category_id, 'operator'  => 'IN']]]));

            if ( $products ) { ?>
                <div id="woox-archive-wrap" class="woox-archive-wrap <?php echo $shortcode_style; ?>">
                <?php
                    if ( $shortcode_style == 'internship' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/internship.php');
                    } else if ( $shortcode_style == 'scholarships' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/scholarships.php');
                    } else if ( $shortcode_style == 'projects' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/projects.php');
                    } else if ( $shortcode_style == 'study-aborad' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/study-aborad.php');
                    } else if ( $shortcode_style == 'international-student-exchange' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/international-student-exchange.php');
                    } else if ( $shortcode_style == 'volunteering' ) {
                        include( plugin_dir_path( __FILE__ ) . 'cards/volunteering.php');
                    }
                ?>
                <div id="shortcode-cat" style="display: none;"><?php echo $category_id; ?></div>
                <div id="shortcode-style" style="display: none;"><?php echo $shortcode_style; ?></div>
                </div>
                <?php if($products_count > 3) { ?>
                    <div class="archive-more">
                        <a href="#" id="archive-morelink" class="archive-morelink">See more <img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/icon-more.png"></a>
                        <div class="woox-ajaxmore"></div>
                    </div>
                <?php } ?>
            <?php } else echo '<h3 style=text-align:center;>Not Found</h3>'; ?>
        </div>

    <?php
        return ob_get_clean();
    }

    public function giveaways() {
        ob_start();
        ?>
        
        <div class="woox-archive">
            <?php
            $args = array (
                'orderby' => 'date',
                'order' => 'DESC',
                'numberposts' => 3,
                'post_type'   => 'give-aways'
            );
            $giveaways = get_posts( $args );
            $giveaways_count = count(get_posts(['post_type'   => 'give-aways']));

            if ( $giveaways ) { ?>
                <div class="woox-archive-wrap" id="giveways-archive-wrap">
                    <?php
                        foreach ($giveaways as $giveaway) {

                            $remaining_day = get_post_meta( $giveaway->ID, 'drawn_date', true );
                            $current_date = date('Y-m-d h:i:s');
                            $diff_date = abs(strtotime($remaining_day) - strtotime($current_date));

                            $years = floor($diff_date / (365*60*60*24));
                            $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
                            $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                            if ( ! $years ) {
                                if ( ! $months ) {
                                    if ( ! $days ) {
                                        $final_difference = 'Error';
                                    } else {
                                        $final_difference = $days.' days';
                                    }
                                } else {
                                    $final_difference = $months.' month';
                                }
                            } else {
                                $final_difference = $years.' year';
                            }

                            $giveway_cats = get_the_terms( $giveaway->ID, 'give-away-category' );
                            //$this->dump($giveaway); ?>
                            <div class="archive-card">
                                <div class="imagewrap">
                                    <?php 
                                        if ( has_post_thumbnail( $giveaway->ID ) ) {
                                            echo '<a href="' . get_permalink( $giveaway->ID ) . '" title="' . esc_attr( $giveaway->post_title ) . '">';
                                            echo get_the_post_thumbnail( $giveaway->ID, 'medium' );
                                            echo '</a>';
                                        } else {
                                            echo '<a href="' . get_permalink( $giveaway->ID ) . '" title="' . esc_attr( $giveaway->post_title ) . '">';
                                            echo '<img src="'.plugin_dir_url( __FILE__ ).'images/giveaway.jpg'.'">';
                                            echo '</a>';
                                        }
                                    ?>
                                    <span class="role"><?php echo $giveway_cats[0]->name; ?></span>
                                </div>
                                <div class="title-wrap title-giveaway">
                                    <h3><a href="<?php echo get_permalink( $giveaway->ID ); ?>"><?php echo $giveaway->post_title; ?></a></h3>
                                    <div class="remaining-day"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/clock.png"><?php echo $final_difference; ?></div>
                                </div>
                                <div class="excerpt">
                                <?php echo get_the_excerpt( $giveaway->ID ); ?>
                                </div>
                                <div class="view-project">
                                    <a href="<?php echo get_permalink( $giveaway->ID ); ?>">View Program</a>
                                </div>
                            </div>
                        <?php }
                    ?>
                </div>
                <?php if($giveaways_count > 3) { ?>
                    <div class="archive-more">
                        <a href="#" id="giveway-morelink" class="archive-morelink">See more <img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/icon-more.png"></a>
                        <div class="giveway-ajaxmore"></div>
                    </div>
                <?php } ?>
            <?php } else echo '<h3 style=text-align:center;>Not Found</h3>'; ?>
        </div>

    <?php
        return ob_get_clean();
    }

    public function campaign() {
        ob_start();
        ?>
        
        <div class="woox-archive">
            <?php
            $args = array (
                'orderby' => 'date',
                'order' => 'DESC',
                'numberposts' => 3,
                'post_type'   => 'campaign'
            );
            $campaigns = get_posts( $args );
            $campaigns_count = count(get_posts(['post_type' => 'campaign']));

            if ( $campaigns ) { ?>
                <div class="woox-archive-wrap" id="campaign-archive-wrap">
                    <?php
                        foreach ($campaigns as $campaign) {
                            //$this->dump($campaign);
                            $remaining_day = $campaign->post_date;
                            $current_date = date('Y-m-d h:i:s');

                            $diff_date = abs(strtotime($remaining_day) - strtotime($current_date));

                            $years = floor($diff_date / (365*60*60*24));
                            $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
                            $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                            if ( ! $years ) {
                                if ( ! $months ) {
                                    if ( ! $days ) {
                                        $final_difference = 'Moments ago';
                                    } else {
                                        $final_difference = 'Posted '.$days.' days ago';
                                    }
                                } else {
                                    $final_difference = 'Posted '.$months.' month ago';
                                }
                            } else {
                                $final_difference = 'Posted '.$years.' years ago';
                            }

                            $campaign_cats = get_the_terms( $campaign->ID, 'campaign_category' );
                            $current_locations = get_the_terms($campaign->ID , 'location-country');
                            //$this->dump($giveaway); ?>
                            <div class="archive-card">
                                <div class="imagewrap">
                                    <?php 
                                        if ( has_post_thumbnail( $campaign->ID ) ) {
                                            echo '<a href="' . get_permalink( $campaign->ID ) . '" title="' . esc_attr( $campaign->post_title ) . '">';
                                            echo get_the_post_thumbnail( $campaign->ID, 'medium' );
                                            echo '</a>';
                                        } else {
                                            echo '<a href="' . get_permalink( $campaign->ID ) . '" title="' . esc_attr( $campaign->post_title ) . '">';
                                            echo '<img src="'.plugin_dir_url( __FILE__ ).'images/giveaway.jpg'.'">';
                                            echo '</a>';
                                        }
                                    ?>
                                    <span class="role"><?php echo $campaign_cats[0]->name; ?></span>
                                </div>
                                <div class="title-wrap title-giveaway">
                                    <h3><a href="<?php echo get_permalink( $campaign->ID ); ?>"><?php echo $campaign->post_title; ?></a></h3>
                                </div>
                                <div class="metawrap">
                                    <div class="meta"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/location.png"><?php echo $current_locations[0]->name; ?></div>
                                    <div class="meta"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/clock.png"><?php echo $final_difference; ?></div>
                                </div>
                                <div class="excerpt">
                                <?php echo get_the_excerpt( $campaign->ID ); ?>
                                </div>
                                <div class="view-project">
                                    <a href="<?php echo get_permalink( $campaign->ID ); ?>">View Program</a>
                                </div>
                            </div>
                        <?php }
                    ?>
                </div>
                <?php if( $campaigns_count > 3 ) { ?>
                    <div class="archive-more">
                        <a href="#" id="campaign-morelink" class="archive-morelink">See more <img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/icon-more.png"></a>
                        <div class="campaign-ajaxmore"></div>
                    </div>
                <?php } ?>
            <?php } else echo '<h3 style=text-align:center;>Not Found</h3>'; ?>
        </div>

    <?php
        return ob_get_clean();
    }

    public function archive_loadmore() {
        $shortcodecat = sanitize_text_field($_POST['shortcodecat']);
        $shortcodestyle = sanitize_text_field($_POST['shortcodestyle']);
        
        $prod_args = array (
            'limit' => 3,
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => $_POST['paged'],
            'tax_query' => array(
                array (
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'     =>  $shortcodecat,
                    'operator'  => 'IN'
                ),
            ),
        );
        $products = wc_get_products($prod_args);
        $ajax_count = count($products);
        $archive_card = '';
        
        if ( $shortcodestyle == 'internship' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/internship.php');
        } else if ( $shortcodestyle == 'scholarships' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/scholarships.php');
        } else if ( $shortcodestyle == 'projects' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/projects.php');
        } else if ( $shortcodestyle == 'study-aborad' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/study-aborad.php');
        } else if ( $shortcodestyle == 'international-student-exchange' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/international-student-exchange.php');
        } else if ( $shortcodestyle == 'volunteering' ) {
            include( plugin_dir_path( __FILE__ ) . 'cards-ajax/volunteering.php');
        }

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => $archive_card, 'ajaxcount' => $ajax_count ]);
        exit();
    }

    public function country_loadmore() {
        $countryPage = sanitize_text_field($_POST['countryPage']);
        $args = array(
            'taxonomy'      => 'location-country',
            'hide_empty'    => false,
            'orderby'       => 'name',
			'order'         => 'ASC',
            'offset'         => $countryPage,
            'number'        => 3
        );
        $all_locations = get_categories( $args );
        $total_locations = count ( $all_locations );
        $location_card = '';
        foreach ($all_locations as $location) {
            $term_link = get_term_link($location->term_id);
            $location_imgid = get_term_meta( $location->term_id, 'location_background_image' );
            $location_img = wp_get_attachment_image($location_imgid[0], 'medium');
            ( $location_img ) ? $location_img : $location_img = '<img src="'.esc_url( plugins_url( 'images/country.jpg', __FILE__ ) ).'">';
            $location_card .= '<a class="location-card" href="'.esc_url($term_link).'">'.$location_img.'<h4><img class="icon" src="'.plugin_dir_url( __FILE__ ).'images/location.png">'.$location->name.'</h4>';
        }

        echo json_encode(['status'=>'ok', 'message' => 'Countries Loaded', 'data' => $location_card, 'totallocations' => $total_locations ]);
        exit();
    }

    public function giveway_loadmore() {
        $givewayPage = sanitize_text_field($_POST['givewayPage']);
        
        $args = array (
            'orderby' => 'date',
            'order' => 'DESC',
            'numberposts' => 3,
            'offset' => $givewayPage,
            'post_type'   => 'give-aways'
        );
        $giveaways = get_posts( $args );
        $giveaways_count = count($giveaways);

        $archive_card = '';

        foreach ($giveaways as $giveaway) {
            $remaining_day = get_post_meta( $giveaway->ID, 'drawn_date', true );
            $current_date = date('Y-m-d h:i:s');
            $diff_date = abs(strtotime($remaining_day) - strtotime($current_date));

            $years = floor($diff_date / (365*60*60*24));
            $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            if ( ! $years ) {
                if ( ! $months ) {
                    if ( ! $days ) {
                        $final_difference = 'Error';
                    } else {
                        $final_difference = $days.' days';
                    }
                } else {
                    $final_difference = $months.' month';
                }
            } else {
                $final_difference = $years.' year';
            }

            if ( has_post_thumbnail( $giveaway->ID ) ) {
                $giveway_img = '<a href="' . get_permalink( $giveaway->ID ) . '" title="' . esc_attr( $giveaway->post_title ) . '">'.get_the_post_thumbnail( $giveaway->ID, 'medium' ).'</a>';
            } else {
                $giveway_img = '<a href="' . get_permalink( $giveaway->ID ) . '" title="' . esc_attr( $giveaway->post_title ) . '"><img src="'.plugin_dir_url( __FILE__ ).'images/giveaway.jpg'.'"></a>';
            }

            $giveway_cats = get_the_terms( $giveaway->ID, 'give-away-category' );

            $archive_card .= '<div class="archive-card">
                                <div class="imagewrap">
                                    '.$giveway_img.'
                                    <span class="role">'.$giveway_cats[0]->name.'</span>
                                </div>
                                <div class="title-wrap title-giveaway">
                                    <h3><a href="'.get_permalink( $giveaway->ID ).'">'.$giveaway->post_title.'</a></h3>
                                    <div class="remaining-day"><img src="'.plugin_dir_url( __FILE__ ).'images/clock.png">'.$final_difference.'</div>
                                </div>
                                <div class="excerpt">
                                '.get_the_excerpt( $giveaway->ID ).'
                                </div>
                                <div class="view-project">
                                    <a href="'.get_permalink( $giveaway->ID ).'">View Program</a>
                                </div>
                            </div>';
            ?>
            
        <?php }

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => $archive_card, 'ajaxcount' => $giveaways_count ]);
        exit();
    }

    public function campaign_loadmore() {
        $campaignPage = sanitize_text_field($_POST['campaignPage']);
        
        $args = array (
            'orderby' => 'date',
            'order' => 'DESC',
            'numberposts' => 3,
            'offset' => $campaignPage,
            'post_type'   => 'campaign'
        );
        $campaigns = get_posts( $args );
        $campaigns_count = count($campaigns);

        $archive_card = '';

        foreach ($campaigns as $campaign) {
            $remaining_day = $campaign->post_date;
            $current_date = date('Y-m-d h:i:s');
            $diff_date = abs(strtotime($remaining_day) - strtotime($current_date));

            $years = floor($diff_date / (365*60*60*24));
            $months = floor(($diff_date - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff_date - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            if ( ! $years ) {
                if ( ! $months ) {
                    if ( ! $days ) {
                        $final_difference = 'Moments ago';
                    } else {
                        $final_difference = $days.' days';
                    }
                } else {
                    $final_difference = $months.' month';
                }
            } else {
                $final_difference = $years.' year';
            }

            if ( has_post_thumbnail( $campaign->ID ) ) {
                $giveway_img = '<a href="' . get_permalink( $campaign->ID ) . '" title="' . esc_attr( $campaign->post_title ) . '">'.get_the_post_thumbnail( $campaign->ID, 'medium' ).'</a>';
            } else {
                $giveway_img = '<a href="' . get_permalink( $campaign->ID ) . '" title="' . esc_attr( $campaign->post_title ) . '"><img src="'.plugin_dir_url( __FILE__ ).'images/giveaway.jpg'.'"></a>';
            }

            $campaign_cats = get_the_terms( $campaign->ID, 'campaign_category' );
            $current_locations = get_the_terms($campaign->ID , 'location-country');

            $archive_card .= '<div class="archive-card">
                                <div class="imagewrap">
                                    '.$giveway_img.'
                                    <span class="role">'.$campaign_cats[0]->name.'</span>
                                </div>
                                <div class="title-wrap title-giveaway">
                                    <h3><a href="'.get_permalink( $campaign->ID ).'">'.$campaign->post_title.'</a></h3>
                                </div>
                                <div class="metawrap">
                                    <div class="meta"><img src="'.plugin_dir_url( __FILE__ ).'images/location.png">'.$current_locations[0]->name.'</div>
                                    <div class="meta"><img src="'.plugin_dir_url( __FILE__ ).'images/clock.png">'.$final_difference.'</div>
                                </div>
                                <div class="excerpt">'.get_the_excerpt( $campaign->ID ).'</div>
                                <div class="view-project">
                                    <a href="'.get_permalink( $campaign->ID ).'">View Program</a>
                                </div>
                            </div>';
            ?>
            
        <?php }

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => $archive_card, 'ajaxcount' => $campaigns_count ]);
        exit();
    }

    public function dump ($val) {
        echo '<pre>';
        var_dump($val);
        echo '</pre>';
    }
        
}

new Woox_Archive_Products();