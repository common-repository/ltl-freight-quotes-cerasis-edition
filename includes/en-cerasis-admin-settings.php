<?php
/**
 * Admin Settings | all admin settings defined
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/**
* Admin Settings | all admin settings defined for plugin usage
*/

class En_Cerasis_Admin_Settings
{

    /**
     * admin settings constructor
     */
    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'admin_settings_scripts') );  
        add_action('init', array($this, 'en_cerasis_save_carrier_status'));
        add_action( 'admin_enqueue_scripts', array($this, 'admin_validation_styles_scripts') );
        add_filter('woocommerce_package_rates', array($this, 'hide_shipping_based_on_class'));
        if (!function_exists('create_ltl_class')) {
            $this->create_ltl_class();
        }
        add_filter( 'woocommerce_no_shipping_available_html', array($this, 'cerasis_shipping_message' ));
        add_filter( 'woocommerce_cart_no_shipping_available_html', array($this, 'cerasis_shipping_message' ));
    } 
    
    /**
     * admin settings scripts calling
     */
    public function admin_settings_scripts()
    {
        require_once plugin_dir_path(__FILE__).'../assets/js/en-cerasis-settings-js.php';
        new En_Cerasis_Settings_Js();
    }
    
    /**
    * Load CSS And JS Scripts
    */
    public function admin_validation_styles_scripts() {  
//        wp_enqueue_script( 'custom_script', plugin_dir_url(dirname(__FILE__)). 'assets/js/en-cerasis-warehouse-dropship.js', array(), '1.5',true );
//        wp_localize_script('custom_script', 'script', array('pluginsUrl' => plugins_url(),));
        wp_register_style( 'custom_style', plugin_dir_url(dirname(__FILE__)). 'assets/css/en-cerasis-custom-style.css', array(), '1.0.6','screen' );
        wp_enqueue_style( 'custom_style' );
    }
    
    /**
     * Save Freight Carriers
     * @param $post_id
     */
    public function en_cerasis_save_carrier_status( $post_id )
    {   
        $postData = $_POST;
        $actionStatus = ( isset( $postData['action'] ) ) ? sanitize_text_field($postData['action']) : "";
        
        if (isset($actionStatus) && $actionStatus == 'en_cerasis_save_carrier_status') {
            global $wpdb;
			$carriers_table = $wpdb->prefix . "carriers";
            $ltl_carriers = $wpdb->get_results("SELECT `id`, `carrier_scac`, `carrier_name`, `carrier_status`,`plugin_name` FROM ".$carriers_table." WHERE `plugin_name` = 'cerasis_ltl_carriers' ORDER BY `carrier_name` ASC");
            foreach ($ltl_carriers as $carriers_value):
                $carrier_scac = ( isset( $postData[$carriers_value->carrier_scac . $carriers_value->id] ) ) ? sanitize_text_field($postData[$carriers_value->carrier_scac . $carriers_value->id]) : "";
                $liftgate_fee = ( isset( $postData[$carriers_value->carrier_scac . $carriers_value->id . "liftgate_fee"] ) ) ? sanitize_text_field($postData[$carriers_value->carrier_scac . $carriers_value->id . "liftgate_fee"]) : "";
                if (isset($carrier_scac) && $carrier_scac == 'on') {
                    $wpdb->query($wpdb->prepare("UPDATE ".$carriers_table." SET `carrier_status` = '%s' , `liftgate_fee` = '$liftgate_fee' WHERE `carrier_scac` = '$carriers_value->carrier_scac' AND `plugin_name` Like 'cerasis_ltl_carriers'", '1'));
                } else {

                    $wpdb->query($wpdb->prepare("UPDATE ".$carriers_table." SET `carrier_status` = '%s' , `liftgate_fee` = '$liftgate_fee' WHERE `carrier_scac` = '$carriers_value->carrier_scac' AND `plugin_name` Like 'cerasis_ltl_carriers' ", '0'));
                }
            endforeach;
        }
    }
    
    /**
     * Hide Shipping Methods If Not From Eniture
     * @param $available_methods
     */
    function hide_shipping_based_on_class($available_methods)
    {
        if (get_option('wc_settings_cerasis_allow_other_plugins') == 'no') {
            if (count($available_methods) > 0) {
                $plugins_array = array();
                $eniture_plugins = get_option('EN_Plugins');
                if ($eniture_plugins) {
                    $plugins_array = json_decode($eniture_plugins);
                }
                foreach ( $available_methods as $index => $method ) {
                    if ( !($method->method_id == 'speedship' || $method->method_id == 'ltl_shipping_method' || in_array( $method->method_id, $plugins_array )) ) {
                    unset($available_methods[$index]);
                    }
                }
            }
        }
        return $available_methods;
    }
    
    /**
     * getting handling fee
     */
    public function get_handling_fee()
    {
        return $handling_fee = get_option('wc_settings_cerasis_hand_free_mark_up');
    }
    
    /**
     * check status for other plugins
     */
    public function other_plugins_status()
    {
        return $other_plugin_status = get_option('wc_settings_cerasis_allow_other_plugins');
    } 
    
    /**
     * create LTL class function
     */
    function create_ltl_class() {        
        wp_insert_term('LTL Freight', 'product_shipping_class', array(
            'description' => 'The plugin is triggered to provide LTL freight quote when the shopping cart contains an item that has a designated shipping class. Shipping class? is a standard WooCommerce parameter not to be confused with freight class? or the NMFC classification system.',
            'slug' => 'ltl_freight'
            )
        );
    }
    /**
     * No Shipping Available Message
     * @param $message
     * @return string
     */
    function cerasis_shipping_message( $message ) {
            return __( 'There are no carriers available for this shipment please contact with store owner' );
    }
}