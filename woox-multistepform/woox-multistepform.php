<?php 
/*
Plugin Name: Woox Multistep form
Plugin URI: #
Description: Multistep form that creates custom woocommerce order.. short code is [msform]
Version: 1.0
Author: WooXperto LLC
Author URI: https://www.wooxperto.com/
*/

if ( ! defined( 'ABSPATH' ) ){
    exit;
}

class Woox_Multistepform {

    public function __construct(){
        add_shortcode( 'msform', [$this, 'ms_form'] );
        add_shortcode( 'studentexchange', [$this, 'studentexchange'] );
        add_action('wp_enqueue_scripts', [$this, 'ms_form_css_js']);
        add_action('init', [$this, 'other_func']);
        add_action( 'woocommerce_after_single_product', [$this, 'woocommerce_after_single_product_apply'] );
    }

    public function other_func(){
        add_action('wp_ajax_get_product_list', [$this, 'get_product_list']);
        add_action('wp_ajax_nopriv_get_product_list', [$this, 'get_product_list']);

        add_action('wp_ajax_get_product_meta', [$this, 'get_product_meta']);
        add_action('wp_ajax_nopriv_get_product_meta', [$this, 'get_product_meta']);

        add_action('wp_ajax_step4_product_details', [$this, 'step4_product_details']);
        add_action('wp_ajax_nopriv_step4_product_details', [$this, 'step4_product_details']);

        add_action('wp_ajax_final_submit', [$this, 'final_submit']);
        add_action('wp_ajax_nopriv_final_submit', [$this, 'final_submit']);
    }

    public function ms_form_css_js() {
        wp_enqueue_style( 'mscss', plugins_url( 'woox-multistepform.css', __FILE__ ) );
        wp_enqueue_script( 'msjs', plugins_url( 'woox-multistepform.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_style( 'select2css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ) );
        wp_localize_script( 'msjs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
    }

    function ms_form() {
        ob_start(); ?>

        <div class="msform-header">
            <div class="msh1">
                <div class="msh-stepno activeheader">01</div>
                <div class="msh-steptext">Programs</div>
            </div>
            <span class="step-sep"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/step-sep.png"></span>
            <div class="msh2">
                <div class="msh-stepno">02</div>
                <div class="msh-steptext">Programs Details</div>
            </div>
            <span class="step-sep"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/step-sep.png"></span>
            <div class="msh3">
                <div class="msh-stepno">03</div>
                <div class="msh-steptext">Personal Details</div>
            </div>
            <span class="step-sep"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/step-sep.png"></span>
            <div class="msh4">
                <div class="msh-stepno">04</div>
                <div class="msh-steptext">Payment Details</div>
            </div>
        </div>

        <form action="" class="msform" id="msform">

            <?php include( plugin_dir_path( __FILE__ ) . 'steps/woox-step1.php'); ?>
            <?php include( plugin_dir_path( __FILE__ ) . 'steps/woox-step2.php'); ?>
            <?php include( plugin_dir_path( __FILE__ ) . 'steps/woox-step3.php'); ?>
            <?php include( plugin_dir_path( __FILE__ ) . 'steps/woox-step4.php'); ?>

            <div class="msform-footer">
                <button id="msprev" class="msbutton" onclick="stepchange(event, 'msprev')" disabled>PREVIOUS</button>
                <button id="msnext" class="msbutton" onclick="stepchange(event, 'msnext')" disabled>NEXT</button>
            </div>

        </form>

    <?php
    return ob_get_clean();    
    }

    function studentexchange() {
        ob_start();
        
        if ( is_product_category( 71 ) ) {
            echo 'working';
        }
        
        ?>
ffffffff

    <?php
    return ob_get_clean();    
    }

    public function get_product_list() {
        $ajax_category_id = sanitize_text_field($_POST['cid']);

        $prod_args = array (
            'limit' => -1,
            'tax_query' => array(
                array (
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'     =>  $ajax_category_id,
                    'operator'  => 'IN'
                ),
            ),
        );

        $products = wc_get_products($prod_args);
    
        $product_options = '<option value="">-----</option>';
        foreach($products as $product)
        {
            $product_options .= '<option value="'.$product->get_id().'">'.$product->get_title().'</option>';
        }

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => $product_options ]);
        exit();
    }

    public function get_product_meta() {
        $ajax_product_id = sanitize_text_field($_POST['pid']);
        $ajax_product = wc_get_product($ajax_product_id);
        $ajax_product_type = $ajax_product->get_type();

        $program_details_top = $this->all_product_meta( $ajax_product_id, $ajax_product_type, $ajax_product);

        echo json_encode(['status'=>'ok', 'message' => 'Products data Loaded', 'type' => $ajax_product_type, 'table' => $program_details_top, 'data' => '' ]);
        exit();
    }

    public function all_product_meta( $product_id, $product_type, $obj_product) {
        $industries = get_the_terms( $product_id, 'industry' );
        $industry_names = '';
        foreach ($industries as $industry) {
            $industry_names .= $industry->name.'<span class=industry-sep> / </span>';
        }

        if ( $product_type == 'internship' ) {

            $product_meta['industry_names'] = $industry_names;
            $product_meta['job_level'] = get_field( 'job_level', $product_id );
            $product_meta['salary'] = get_field( 'salary', $product_id );
            $product_meta['experience'] = get_field( 'experience', $product_id );
            $product_meta['job_type'] = get_field( 'job_type', $product_id );
            $product_meta['deadline'] = get_field( 'deadline', $product_id );
            $product_meta['updated'] = $obj_product->get_date_modified()->date('d/m/Y');
            $product_meta['location_address'] = get_field( 'location_address', $product_id );

            $program_details_top = '<div class="program-details-top">
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon1.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Industry</div>
                        <div class="pdt-description">'.$product_meta['industry_names'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon2.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Job Level</div>
                        <div class="pdt-description">'.$product_meta['job_level'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon3.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Salary</div>
                        <div class="pdt-description">'.$product_meta['salary'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon4.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Experience</div>
                        <div class="pdt-description">'.$product_meta['experience'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon5.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Job type</div>
                        <div class="pdt-description">'.$product_meta['job_type'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon6.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Deadline</div>
                        <div class="pdt-description">'.$product_meta['deadline'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon7.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Updated</div>
                        <div class="pdt-description">'.$product_meta['updated'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon8.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Location</div>
                        <div class="pdt-description">'.$product_meta['location_address'].'</div>
                    </div>
                </div>
            </div>';
        } else if ( $product_type == 'student_exchange' ) {

            $product_meta['majors'] = get_field( 'majors', $product_id );
            $product_meta['citizenship'] = get_field( 'citizenship', $product_id );
            $product_meta['gpa'] = get_field( 'gpa', $product_id );
            $product_meta['document'] = get_field( 'document', $product_id );
            $product_meta['household'] = get_field( 'household', $product_id );
            $product_meta['deadline'] = get_field( 'deadline', $product_id );
            $product_meta['updated'] = $obj_product->get_date_modified()->date('d/m/Y');
            $product_meta['location_address'] = get_field( 'location_address', $product_id );

            $program_details_top = '<div class="program-details-top">
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon1.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Majors</div>
                        <div class="pdt-description">'.$product_meta['majors'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon2.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Citizenship</div>
                        <div class="pdt-description">'.$product_meta['citizenship'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon3.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">GPA</div>
                        <div class="pdt-description">'.$product_meta['gpa'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon4.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Document</div>
                        <div class="pdt-description">'.$product_meta['document'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon5.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Household</div>
                        <div class="pdt-description">'.$product_meta['household'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon6.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Deadline</div>
                        <div class="pdt-description">'.$product_meta['deadline'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon7.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Updated</div>
                        <div class="pdt-description">'.$product_meta['updated'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon8.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Location</div>
                        <div class="pdt-description">'.$product_meta['location_address'].'</div>
                    </div>
                </div>
            </div>';
        } else if ( $product_type == 'project' ) {

            $product_meta['purpose'] = get_field( 'purpose', $product_id );
            $product_meta['start_dates'] = get_field( 'start_dates', $product_id );
            $product_meta['duration'] = get_field( 'duration', $product_id );
            $product_meta['vol_hours'] = get_field( 'vol_hours', $product_id );
            $product_meta['accommodation'] = get_field( 'accommodation', $product_id );
            $product_meta['age'] = get_field( 'age', $product_id );
            $product_meta['fees'] = get_field( 'fees', $product_id );
            $product_meta['included'] = get_field( 'included', $product_id );

            $program_details_top = '<div class="program-details-top">
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon1.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Purpose</div>
                        <div class="pdt-description">'.$product_meta['purpose'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon2.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Start dates</div>
                        <div class="pdt-description">'.$product_meta['start_dates'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon3.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Duration</div>
                        <div class="pdt-description">'.$product_meta['duration'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon4.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Vol hours</div>
                        <div class="pdt-description">'.$product_meta['vol_hours'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon5.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Accommodation</div>
                        <div class="pdt-description">'.$product_meta['accommodation'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon6.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Age</div>
                        <div class="pdt-description">'.$product_meta['age'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon7.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">fees</div>
                        <div class="pdt-description">'.$product_meta['fees'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon8.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Included</div>
                        <div class="pdt-description includelink"><a target="_blank" href="'.$product_meta['included'].'">Here</div>
                    </div>
                </div>
            </div>';
        } else if ( $product_type == 'scholarship' ) {

            $product_meta['majors'] = get_field( 'majors', $product_id );
            $product_meta['citizenship'] = get_field( 'citizenship', $product_id );
            $product_meta['gpa'] = get_field( 'gpa', $product_id );
            $product_meta['document'] = get_field( 'document', $product_id );
            $product_meta['household'] = get_field( 'household', $product_id );
            $product_meta['deadline'] = get_field( 'deadline', $product_id );
            $product_meta['updated'] = $obj_product->get_date_modified()->date('d/m/Y');
            $product_meta['location_address'] = get_field( 'location_address', $product_id );

            $program_details_top = '<div class="program-details-top">
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon1.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Majors</div>
                        <div class="pdt-description industry">'.$product_meta['majors'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon2.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Citizenship</div>
                        <div class="pdt-description job-level">'.$product_meta['citizenship'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon3.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">GPA</div>
                        <div class="pdt-description salary">'.$product_meta['gpa'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon4.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Document</div>
                        <div class="pdt-description experience">'.$product_meta['document'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon5.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Household</div>
                        <div class="pdt-description job-type">'.$product_meta['household'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon6.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Deadline</div>
                        <div class="pdt-description deadline">'.$product_meta['deadline'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon7.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Updated</div>
                        <div class="pdt-description updated">'.$product_meta['updated'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon8.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Location</div>
                        <div class="pdt-description location">'.$product_meta['location_address'].'</div>
                    </div>
                </div>
            </div>';
        } else if ( $product_type == 'volunteering' ) {

            $product_meta['purpose'] = get_field( 'purpose', $product_id );
            $product_meta['start_dates'] = get_field( 'start_dates', $product_id );
            $product_meta['duration'] = get_field( 'duration', $product_id );
            $product_meta['vol_hours'] = get_field( 'vol_hours', $product_id );
            $product_meta['accommodation'] = get_field( 'accommodation', $product_id );
            $product_meta['age'] = get_field( 'age', $product_id );
            $product_meta['fees'] = get_field( 'fees', $product_id );
            $product_meta['included'] = get_field( 'included', $product_id );

            $program_details_top = '<div class="program-details-top">
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon1.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Purpose</div>
                        <div class="pdt-description">'.$product_meta['purpose'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon2.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Start dates</div>
                        <div class="pdt-description">'.$product_meta['start_dates'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon3.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Duration</div>
                        <div class="pdt-description">'.$product_meta['duration'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon4.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Vol hours</div>
                        <div class="pdt-description">'.$product_meta['vol_hours'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon5.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Accommodation</div>
                        <div class="pdt-description">'.$product_meta['accommodation'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon6.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Age</div>
                        <div class="pdt-description">'.$product_meta['age'].'</div>
                    </div>
                </div>
                <div class="pdt-column">
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon7.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">fees</div>
                        <div class="pdt-description">'.$product_meta['fees'].'</div>
                    </div>
                    <div class="pdt-column-wrap">
                        <div class="pdt-icon"><img src="'.esc_url( plugins_url( 'images/step2-icon8.png', __FILE__ ) ).'"></div>
                        <div class="pdt-title">Included</div>
                        <div class="pdt-description includelink"><a target="_blank" href="'.$product_meta['included'].'">Here</div>
                    </div>
                </div>
            </div>';
        }
        return $program_details_top;
    }

    public function woocommerce_after_single_product_apply() {
        global $product;
        $product_id = $product->get_id();

        $terms = get_the_terms ( $product_id, 'product_cat' );

        $category_id = $terms[0]->term_id;

        $applylink = site_url().'/apply-now';
        echo '<div class=apply-btn-area><hr><a class=button href='.$applylink.'?product_id='.$product_id.'&category_id='.$category_id.'>Apply Now</a></div>';
    }

    public function step4_product_details() {
        $ajax_product_id = sanitize_text_field($_POST['pid']);
        $product_obj = wc_get_product( $ajax_product_id );
        $ajax_product_price = '$'.$product_obj->get_price();

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => $ajax_product_price ]);
        exit();
    }

    public function final_submit() {
        $category_id = $_POST['program_category'];
        $product_id = sanitize_text_field($_POST['program_product']);
        $expected_salary = sanitize_text_field($_POST['expected_salary']);
        $last_salary = sanitize_text_field($_POST['last_salary']);
        $previous_company = sanitize_text_field($_POST['previous_company']);
        $experience = sanitize_text_field($_POST['experience']);
        $how_soon_join = sanitize_text_field($_POST['how_soon_join']);
        $resume = $_FILES['resume'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $date_of_birth = sanitize_text_field($_POST['date_of_birth']);
        $gender = sanitize_text_field($_POST['gender']);
        $nationality = sanitize_text_field($_POST['nationality']);
        $marital_status = sanitize_text_field($_POST['marital_status']);
        $email_address = sanitize_text_field($_POST['email_address']);
        $phone_number = sanitize_text_field($_POST['phone_number']);
        $address = sanitize_text_field($_POST['address']);
        $city = sanitize_text_field($_POST['city']);
        $zip_code = sanitize_text_field($_POST['zip_code']);
        $country = sanitize_text_field($_POST['country']);
        $payment_type = sanitize_text_field($_POST['payment_method']);
        $payment_name = sanitize_text_field($_POST['payment_name']);

        $resume_id = $this->upload_file_in_wp_media($resume); 
        $resume_url = wp_get_attachment_url( $resume_id );

        $order = new WC_Order();
        $get_product = wc_get_product( $product_id );
        $order->add_product( $get_product );

        $ship_bill_address = array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email_address,
            'phone'      => $phone_number,
            'address_1'  => $address,
            'city'       => $city,
            'postcode'   => $zip_code,
            'country'    => $country
        );
        
        $order->set_address( $ship_bill_address, 'billing' );
        $order->set_address( $ship_bill_address, 'shipping' );
        
        // add payment method
        $order->set_payment_method( $payment_type );
        $order->set_payment_method_title( $payment_name );
        
        
        // add two meta values of the same meta key
        $order->add_meta_data( 'expected_salary', $expected_salary );
        $order->add_meta_data( 'last_salary', $last_salary );
        $order->add_meta_data( 'previous_company', $previous_company );
        $order->add_meta_data( 'experience', $experience );
        $order->add_meta_data( 'how_soon_join', $how_soon_join );
        $order->add_meta_data( 'resume', $resume_url );
        $order->add_meta_data( 'date_of_birth', $date_of_birth );
        $order->add_meta_data( 'gender', $gender );
        $order->add_meta_data( 'nationality', $nationality );
        $order->add_meta_data( 'marital_status', $marital_status );
        
        // calculate, order status and save
        $order_price = $order->calculate_totals();
        if( $order_price > 0 ) {
            $order->set_status('wc-pending');
        } else {
            $order->set_status('wc-completed');
        }

        $order->save();

        echo json_encode(['status'=>'ok', 'message' => 'Products Loaded', 'data' => [$category_id, $product_id, $expected_salary, $last_salary, $previous_company, $experience, $how_soon_join, $resume, $first_name, $last_name, $date_of_birth, $gender, $nationality, $marital_status, $email_address, $phone_number, $address, $city, $zip_code, $country, $payment_type, $payment_name] ]);
        exit();
    }

    /******FILE UPLOAD*****************/
    public function upload_file_in_wp_media( $file = array() ) {    
        require_once( ABSPATH . 'wp-admin/includes/admin.php' );
        $file_return = wp_handle_upload( $file, array('test_form' => false ) );
        if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
            return false;
        } else {
            $filename = $file_return['file'];
            $attachment = array(
                'post_mime_type' => $file_return['type'],
                'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content' => '',
                'post_status' => 'inherit',
                'guid' => $file_return['url']
            );
            $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
            wp_update_attachment_metadata( $attachment_id, $attachment_data );
            if( 0 < intval( $attachment_id ) ) {
                return $attachment_id;
            }
        }
        return false;
    }
        
}

new Woox_Multistepform();