<?php 
/*
Plugin Name: Crypto widget by coin compare
Plugin URI: #
Description: Iframe generator
Version: 1.0
Author: WooXperto LLC
Author URI: https://www.wooxperto.com/
License: GPLv2 or later
Text Domain: crypto-widget
*/

if ( ! defined( 'ABSPATH' ) ){
    exit;
}

class Woox_Crypto_shortcode {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'crypto_widget_iframe_css_js']);
        add_action( 'admin_menu', [$this, 'crypto_widget_add_crypto_page'] );
        add_shortcode( 'cryptocompare-widget', [$this, 'crypto_widget_cryptocompare_widget'] );
        register_activation_hook(__FILE__, [$this, 'crypto_widget_crypto_shortcode_table']);
        add_action('init', [$this, 'crypto_widget_ajax_func']);
    }

    public function crypto_widget_ajax_func() {
        add_action('wp_ajax_crypto_widget_save_crypto_data', [$this, 'crypto_widget_save_crypto_data']);
        add_action('wp_ajax_nopriv_crypto_widget_save_crypto_data', [$this, 'crypto_widget_save_crypto_data']);
        add_action('wp_ajax_crypto_widget_delete_copyshortcode', [$this, 'crypto_widget_delete_copyshortcode']);
        add_action('wp_ajax_nopriv_crypto_widget_delete_copyshortcode', [$this, 'crypto_widget_delete_copyshortcode']);
        add_action('wp_ajax_crypto_widget_edit_crypto_data', [$this, 'crypto_widget_edit_crypto_data']);
        add_action('wp_ajax_nopriv_crypto_widget_edit_crypto_data', [$this, 'crypto_widget_edit_crypto_data']);
    }

    public function crypto_widget_iframe_css_js() {
        wp_enqueue_style( 'iframecss', plugins_url( 'crypto-widget.css', __FILE__ ) );
        wp_enqueue_script( 'iframejs', plugins_url( 'crypto-widget.js', __FILE__ ), array( 'jquery' ),time() );

        wp_enqueue_style( 'modalcss', plugins_url( 'assets/jquery.modal.min.css', __FILE__ ) );
        wp_enqueue_script( 'modaljs', plugins_url( 'assets/jquery.modal.min.js', __FILE__ ), array( 'jquery' ) );

        wp_enqueue_style( 'select2css', plugins_url( 'assets/select2.min.css', __FILE__ ) );
        wp_enqueue_script( 'select2js', plugins_url( 'assets/select2.min.js', __FILE__ ), array( 'jquery' ) );

        wp_enqueue_style( 'datatablecss', plugins_url( 'assets/dataTables.dataTables.min.css', __FILE__ ) );
        wp_enqueue_script( 'datatablejs', plugins_url( 'assets/dataTables.min.js', __FILE__ ), array( 'jquery' ) );
        wp_localize_script( 'iframejs', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
    }

    public function crypto_widget_cryptocompare_widget($atts) {
        $atts = shortcode_atts(
            array(
                'type' => 'sell',
                'coin' => 'bitcoin',
                'text-color' => 'ffffff',
                'bg-color' => '0051FF',
                'height' => 550,
                'width' => 500,
            ),
            $atts
        );
        ob_start();

        $sc_type = $atts['type'];
        $sc_coin = $atts['coin'];
        $sc_text_color = $atts['text-color'];
        $sc_bg_color = $atts['bg-color'];
        $sc_height = $atts['height'];
        $sc_width = $atts['width'];
        ?>

        <iframe src="https://coincompare.net/cryptoprijzen-widget?type=<?php echo esc_attr( $sc_type ); ?>&coin=rocket-pool&btn_text_color=<?php echo esc_attr( $sc_text_color ); ?>&btn_bg_color=<?php echo esc_attr( $sc_bg_color ); ?>" height="<?php echo esc_attr( $sc_height ); ?>" frameborder="0" scrolling="no" width="<?php echo esc_attr( $sc_width ); ?>"></iframe>

        <?php
        return ob_get_clean();
    }

    public function crypto_widget_crypto_shortcode_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'crypto_shortcode_table';
        $sql = "CREATE TABLE `$table_name` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `crypto_type` VARCHAR(10) DEFAULT NULL,
            `crypto_width` INT DEFAULT NULL,
            `crypto_height` INT DEFAULT NULL,
            `coin_type` VARCHAR(100) DEFAULT NULL,
            `crypto_txt_color` VARCHAR(10) DEFAULT NULL,
            `crypto_bg_color` VARCHAR(10) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
      
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          dbDelta($sql);
        }
    }

    public function crypto_widget_add_crypto_page() {
        add_menu_page(
            __( 'Coin Compair', 'crypto-widget' ),
            'Coin Compair',
            'manage_options',
            'crypto-widget-coins',
            [$this, 'crypto_widget_coins'],
            'dashicons-shortcode'
        );
    }

    public function crypto_widget_coins() { ?>
        <div class="wrap crypto-wrap">
            <h1 class="wp-heading-inline">Coin Compare Widgets</h1>
            <a href="#crypto-form" id="add_shortcode" class="page-title-action" rel="modal:open" onclick="add_shortcode()">Add new</a>

            <table id="cryptotable" class="display wp-list-table widefat cryptotable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shortcode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    global $wpdb;
                    $cryptodata_table = $wpdb->prefix . 'crypto_shortcode_table';
                    $cryptodatas = $wpdb->get_results( "SELECT * FROM $cryptodata_table");
                    $static_id = 0;
                    if ( $cryptodatas ) {
                        foreach ( $cryptodatas as $cryptodata ) {
                            $static_id++;
                            $crypto_type = $cryptodata->crypto_type;
                            $crypto_coin_type = $cryptodata->coin_type;
                            $crypto_txt_color = $cryptodata->crypto_txt_color;
                            if ( $crypto_txt_color ) {
                                $crypto_txt_color = str_replace('#', '', $crypto_txt_color);
                            }
                            $crypto_bg_color = $cryptodata->crypto_bg_color;
                            if ( $crypto_bg_color ) {
                                $crypto_bg_color = str_replace('#', '', $crypto_bg_color);
                            }
                            $crypto_width = $cryptodata->crypto_width;
                            if ( $crypto_width <= 0 ) {
                                $crypto_width = 500;
                            }
                            $crypto_height = $cryptodata->crypto_height;
                            if ( $crypto_height <= 0 ) {
                                $crypto_height = 550;
                            }

                            $crypto_shortcode = '[cryptocompare-widget type="'.$crypto_type.'" coin="'.$crypto_coin_type.'" text-color="'.$crypto_txt_color.'" bg-color="'.$crypto_bg_color.'" height="'.$crypto_height.'px" width="'.$crypto_width.'px"]';
                            ?>
                            
                            <tr>
                                <td><?php echo esc_html($static_id); ?><span style="display: none;" id="origin_id<?php echo esc_attr($static_id); ?>"><?php echo esc_html($cryptodata->id); ?></td>
                                <td id="shortcodedata<?php echo esc_attr($static_id); ?>"><?php echo esc_html($crypto_shortcode); ?></td>
                                <td><span class="dashicons dashicons-admin-page" onclick="copy_shortcode('shortcodedata<?php echo esc_attr($static_id); ?>')" id="copyshortcode<?php echo esc_attr($static_id); ?>"></span><span class="dashicons dashicons-edit" id="editshortcode" onclick="edit_shortcode('alldata<?php echo esc_attr($static_id); ?>')"></span><span id="deleteshortcode" class="dashicons dashicons-trash" onclick="delete_shortcode('origin_id<?php echo esc_attr($static_id); ?>')"></span></td>
                            </tr>

                            <div id="alldata<?php echo esc_attr( $static_id ); ?>" style="display: none;">
                                <div id="static_id"><?php echo esc_attr( $static_id ); ?></div>
                                <div id="origin_id"><?php echo esc_attr( $cryptodata->id ); ?></div>
                                <div id="crypto_type"><?php echo esc_attr( $crypto_type ); ?></div>
                                <div id="crypto_coin_type"><?php echo esc_attr( $crypto_coin_type ); ?></div>
                                <div id="crypto_txt_color"><?php echo esc_attr( $crypto_txt_color ); ?></div>
                                <div id="crypto_bg_color"><?php echo esc_attr( $crypto_bg_color ); ?></div>
                                <div id="crypto_width"><?php echo esc_attr( $crypto_width ); ?></div>
                                <div id="crypto_height"><?php echo esc_attr( $crypto_height ); ?></div>
                            </div>

                        <?php }
                        
                    }
                ?>
                    
                </tbody>
            </table>
            <div style="display: none;" id="totalshortcode"><?php echo esc_attr( $static_id ); ?></div>



            <form id="crypto-form" class="modal crypto-form" action="" method="POST">

                <h2 id="cmodal-title"><?php esc_html__('Create Widget','crypto-widget');?></h2>

                <div class="twocol">
                    <div class="eachinput">
                        <label for="crypto-type">Type</label>
                        <select name="crypto_type" id="crypto-type" class="selec2plug">
                            <option value="buy">Buy</option>
                            <option value="sell">Sell</option>
                        </select>
                    </div>

                    <div class="eachinput">
                        <label for="coin-type">Coin</label>
                        <select name="coin_type" id="coin-type" class="selec2plug">

                        <?php
                            $responses = wp_remote_get( 'https://admin.coincompare.net/api/all-coins' );
                            $bodys     = wp_remote_retrieve_body( $responses );
                            $alldata = json_decode($bodys, true);

                            foreach( $alldata as $data ) {
                                echo '<option value="'.esc_attr($data['slug']).'">'.esc_attr($data['coin_full_name']).'</option>';
                            }
                        ?>
                        </select>
                    </div>
                </div>

                <div class="twocol">
                    <div class="eachinput">
                        <label for="crypto-width">Width</label>
                        <input type="number" id="crypto-width" name="crypto_width" class="cryptowidth"> px
                    </div>

                    <div class="eachinput">
                        <label for="crypto-height">Height</label>
                        <input type="number" id="crypto-height" name="crypto_height" class="cryptoheight"> px
                    </div>
                </div>

                <div class="twocol">
                    <div class="eachinput">
                        <label for="crypto-txt-color">Button Text Color</label>
                        <input type="color" id="crypto-txt-color" name="crypto_txt_color">
                    </div>

                    <div class="eachinput">
                        <label for="crypto-bg-color">Button BG Color</label>
                        <input type="color" id="crypto-bg-color" name="crypto_bg_color">
                    </div>
                </div>

                <div class="eachinput">
                    <?php
                        wp_nonce_field('save_edit_delete', 'name_of_your_nonce_field');
                    ?>
                    <input type="button" id="submit-crypto" class="save-crypto" value="Save" onclick="save_shortcode()">
                    <input type="button" id="edit-crypto" class="save-crypto" value="Edit" style="display: none;">
                    <div id="responsemessage"></div>
                </div>

            </form>
        </div>
        
    <?php }

    public function crypto_widget_save_crypto_data() {
        // nonce verify
        if ( isset($_POST['save_nonce']) ) {
            if(wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['save_nonce'])), 'save_edit_delete')) {

                global $wpdb;
                $table_name = $wpdb->prefix . 'crypto_shortcode_table';
    
                $crypto_type = '';
                if(isset($_POST['crypto_type'])) {
                    $crypto_type = sanitize_text_field(wp_unslash($_POST['crypto_type']));
                }
    
                if(isset($_POST['crypto_width'])) {
                    $crypto_width = sanitize_text_field(wp_unslash($_POST['crypto_width']));
                }
    
                if(isset($_POST['crypto_height'])) {
                    $crypto_height = sanitize_text_field(wp_unslash($_POST['crypto_height']));
                }
    
                if(isset($_POST['coin_type'])) {
                    $coin_type = sanitize_text_field(wp_unslash($_POST['coin_type']));
                }
    
                if(isset($_POST['crypto_txt_color'])) {
                    $crypto_txt_color = sanitize_text_field(wp_unslash($_POST['crypto_txt_color']));
                }
    
                if(isset($_POST['crypto_bg_color'])) {
                    $crypto_bg_color = sanitize_text_field(wp_unslash($_POST['crypto_bg_color']));
                }
                
                
    
                $cryptodata = array(
                    'crypto_type' => $crypto_type,
                    'crypto_width' => $crypto_width,
                    'crypto_height' => $crypto_height,
                    'coin_type' => $coin_type,
                    'crypto_txt_color' => $crypto_txt_color,
                    'crypto_bg_color' => $crypto_bg_color,
                );
                $format = array( '%s', '%d', '%d', '%s', '%s', '%s' );
                $inserted = $wpdb->insert( $table_name, $cryptodata, $format );
    
                // echo json_encode(['status'=>'ok', 'message' => 'Saved', 'data' => $inserted ]);
                echo wp_json_encode(['status'=>'ok', 'message' => 'Saved', 'data' => $inserted ]);
            } else {
                // echo json_encode(['status'=>'not-ok', 'message' => 'Nonce not verified', 'data' => 0 ]);
                echo wp_json_encode(['status'=>'not-ok', 'message' => 'Nonce not verified', 'data' => 0 ]);
            }
        }
        
        exit();
    }


    public function crypto_widget_edit_crypto_data() {

        if ( isset($_POST['edit_nonce']) ) {
            if(wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['edit_nonce'])), 'save_edit_delete')) {
        
                global $wpdb;
                $table_name = $wpdb->prefix . 'crypto_shortcode_table';
    
    
                if(isset($_POST['edited_origin_id'])) {
                    $edited_origin_id = sanitize_text_field(wp_unslash($_POST['edited_origin_id']));
                }
    
                if(isset($_POST['edited_crypto_type'])) {
                    $edited_crypto_type = sanitize_text_field(wp_unslash($_POST['edited_crypto_type']));
                }
    
                if(isset($_POST['edited_crypto_coin_type'])) {
                    $edited_crypto_coin_type = sanitize_text_field(wp_unslash($_POST['edited_crypto_coin_type']));
                }
    
                if(isset($_POST['edited_crypto_txt_color'])) {
                    $edited_crypto_txt_color = sanitize_text_field(wp_unslash($_POST['edited_crypto_txt_color']));
                }
                
                if(isset($_POST['edited_crypto_bg_color'])) {
                    $edited_crypto_bg_color = sanitize_text_field(wp_unslash($_POST['edited_crypto_bg_color']));
                }
    
                if(isset($_POST['edited_crypto_width'])) {
                    $edited_crypto_width = sanitize_text_field(wp_unslash($_POST['edited_crypto_width']));
                }
    
                if(isset($_POST['edited_crypto_height'])) {
                    $edited_crypto_height = sanitize_text_field(wp_unslash($_POST['edited_crypto_height']));
                }
                
                
    
                $edited_cryptodata = array(
                    'crypto_type' => $edited_crypto_type,
                    'crypto_width' => $edited_crypto_width,
                    'crypto_height' => $edited_crypto_height,
                    'coin_type' => $edited_crypto_coin_type,
                    'crypto_txt_color' => $edited_crypto_txt_color,
                    'crypto_bg_color' => $edited_crypto_bg_color
                );
            
                $edited = $wpdb->update( $table_name, $edited_cryptodata, array( 'id' => $edited_origin_id ) );
    
                echo wp_json_encode(['status'=>'ok', 'message' => 'Updated', 'data' => $edited ]);
            } else {
                echo wp_json_encode(['status'=>'not-ok', 'message' => 'Nonce not verified', 'data' => 0 ]);
            }
        }
        exit();
    }


    public function crypto_widget_delete_copyshortcode() {

        if ( isset($_POST['delete_nonce']) ) {
            if(wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['delete_nonce'])), 'save_edit_delete')) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'crypto_shortcode_table';
                
                if ( isset( $_POST['to_delete']) ) {
                    $to_delete = sanitize_text_field(wp_unslash($_POST['to_delete']));
                }
                $inserted = $wpdb->delete( $table_name, array( 'id' => $to_delete ) );
    
                echo wp_json_encode(['status'=>'ok', 'message' => 'Deleted', 'data' => $inserted ]);
            } else {
                echo wp_json_encode(['status'=>'not-ok', 'message' => 'Nonce not verified', 'data' => 0 ]);
            }
        }
        
        exit();
    }
        
}

new Woox_Crypto_shortcode();