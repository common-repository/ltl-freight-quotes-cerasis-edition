<?php
/**
  Plugin Name:  LTL Freight Quotes - Cerasis Edition
  Plugin URI:   https://eniture.com/products/
  Description:  Dynamically retrieves your negotiated shipping rates from Cerasis and displays the results in the WooCommerce shopping cart.
  Version:      2.0.6
  Author:       Eniture Technology
  Author URI:   http://eniture.com/
  Text Domain:  eniture-technology
  License:      GPL version 2 or later - http://www.eniture.com/
  WC requires at least: 3.0.0
  WC tested up to: 4.0.1
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * check plugin activattion 
 */
if (!function_exists('is_plugin_active')) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}



if(!function_exists('en_woo_plans_notification_PD'))
{
    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for($en = 1 ; $en <= 25 ; $en++ )
        {
            $settings = get_option($eniture_plugins_id.$en);
            if(isset($settings) && (!empty($settings)) && (is_array($settings)))
            {
                $plugin_detail   = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if($key != 'plugin_name'){
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name"; 
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter( 'en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1 );
}

if(!function_exists('en_woo_plans_notification_message'))
{
    function en_woo_plans_notification_message($enable_plugins , $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return  $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action' , 'en_woo_plans_notification_message' , 10 ,2);
}

add_filter('plugin_action_links', 'wc_cerasis_add_action_plugin', 10, 5);
/**
     * plugin settings and support link at wp plugin.php page
     * @staticvar $plugin
     * @param $actions
     * @param $plugin_file
     * @return string
     */
function wc_cerasis_add_action_plugin( $actions, $plugin_file )
{
    static $plugin;
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file){
        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=cerasis_freights">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://support.eniture.com/home" target="_blank">Support</a>');
        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);
    }
    return $actions;
}

    /**
     * Get Host 
     * @param type $url
     * @return type
     */
    if(!function_exists('getHost')){
        function getHost($url) { 
            $parseUrl = parse_url(trim($url)); 
            if(isset($parseUrl['host'])){
                $host = $parseUrl['host'];
            }else{
                 $path = explode('/', $parseUrl['path']);
                 $host = $path[0];
            }
            return trim($host); 
        }
    }
    
    /**
     * Get Domain Name 
     */
    if(!function_exists('cerasis_get_domain')){
        function cerasis_get_domain(){
            global $wp;
            $url =  home_url( $wp->request );
            return getHost($url);
        }
    }
    
/**
 * Autoloads all classes file called.
 */
require_once plugin_dir_path(__FILE__).'includes/en-cerasis-autoloads.php'; 
require_once( __DIR__.'/orders/orders.php' );
require_once plugin_dir_path(__FILE__).'includes/en-cerasis-shipping-update-change.php'; 
include_once plugin_dir_path(__FILE__) . 'includes/carriers/en-cerasis-carrier-list.php';
require_once( __DIR__.'/includes/en-cerasis-filter-quotes.php' );
require_once( __DIR__.'/includes/en-cerasis-compact.php' );
require_once( __DIR__.'/includes/en-cerasis-liftgate-as-option.php' );
require_once plugin_dir_path(__FILE__) .('includes/warehouse-dropship/wild-delivery.php' );
require_once plugin_dir_path(__FILE__) .('includes/warehouse-dropship/get-distance-request.php' );
require_once plugin_dir_path(__FILE__) .('includes/standard-package-addon/standard-package-addon.php' );
require_once plugin_dir_path(__FILE__) .'update-plan.php';


/**
 * LTL Freight Quotes - Cerasis Edition Activation/Deactivation Hook
 */
register_activation_hook( __FILE__, array( 'En_Cerasis_Install_Uninstall', 'install' ) ); 
register_deactivation_hook( __FILE__, array( 'En_Cerasis_Install_Uninstall', 'uninstall' ) );
register_activation_hook(__FILE__, 'old_store_cerasis_ltl_dropship_status');
register_activation_hook(__FILE__, 'old_store_cerasis_ltl_hazmat_status');
register_activation_hook(__FILE__, 'en_cerasis_freight_activate_hit_to_update_plan');
register_deactivation_hook(__FILE__, 'en_cerasis_freight_deactivate_hit_to_update_plan');


/**
 * Cerasis plugin update now
 * @param array type $upgrader_object
 * @param array type $options
 */
function en_cerasis_update_now() 
{
    $index = 'ltl-freight-quotes-cerasis-edition/ltl-freight-quotes-cerasis-edition.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
    $update_now = get_option('en_cerasis_update_now');
    
    if($update_now != $plugin_version)
    {
        if(!function_exists('en_cerasis_freight_activate_hit_to_update_plan'))
        {
            require_once( __DIR__.'/update-plan.php' );
        }
                
        old_store_cerasis_ltl_dropship_status();
        old_store_cerasis_ltl_hazmat_status();
        en_cerasis_freight_activate_hit_to_update_plan();
        
        update_option('en_cerasis_update_now', $plugin_version);
    }
}

add_action('init', 'en_cerasis_update_now');


define("en_woo_plugin_cerasis_freights" , "cerasis_freights");

add_action( 'wp_enqueue_scripts', 'en_ltl_cerasis_frontend_checkout_script' );
/**
 * Load Frontend scripts for ODFL
 */
function en_ltl_cerasis_frontend_checkout_script() 
{  
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'en_ltl_cerasis_frontend_checkout_script', plugin_dir_url( __FILE__ ) . 'front/js/en-cerasis-checkout.js', array(), '1.0.0' );
    wp_localize_script('en_ltl_cerasis_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

/**
 * Weekly cron
 */

add_filter( 'cron_schedules', 'en_add_every_weekly_cron_get_carriers' );
function en_add_every_weekly_cron_get_carriers( $schedules ) {
    $schedules['every_three_minutes'] = array(
            'interval'  => 60 * 60 * 24 * 7,
            'display'   => __( 'Every Week', 'Cerasis Get Carriers' )
    );
    return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'en_add_every_weekly_cron_get_carriers' ) ) {
    wp_schedule_event( time(), 'every_three_minutes', 'en_add_every_weekly_cron_get_carriers' );
}

// Hook into that action that'll fire every three minutes
add_action( 'en_add_every_weekly_cron_get_carriers', 'every_weekly_event_func' );
function every_weekly_event_func() {
    include_once plugin_dir_path(__FILE__) . 'includes/carriers/en-cerasis-carrier-list.php';
    $En_Cerasis_Carrier_List = new En_Cerasis_Carrier_List();
    $En_Cerasis_Carrier_List->carriers();
}
/**
 * Plans Common Hooks 
 */
add_filter('cerasis_freights_quotes_plans_suscription_and_features' , 'cerasis_freights_quotes_plans_suscription_and_features' , 1);

function cerasis_freights_quotes_plans_suscription_and_features($feature)
{
    $package = get_option('cerasis_freight_package');
    
    $features = array
                (
                    'instore_pickup_local_devlivery'    => array('3'),
                );
                
    if(get_option('cerasis_freight_quotes_store_type') == "1")
    {
        $features['multi_warehouse'] = array('2','3');
        $features['multi_dropship'] = array('','0','1','2','3');
        $features['hazardous_material'] = array('2','3');
    }
    else
    {
        $dropship_status = get_option('en_old_user_dropship_status');
        $warehouse_status = get_option('en_old_user_warehouse_status');
        $hazmat_status = get_option('en_old_user_hazmat_status');
        
        isset($dropship_status) && ($dropship_status == "0") ? $features['multi_dropship'] = array('','0','1','2','3') : '';
        isset($warehouse_status) && ($warehouse_status == "0") ? $features['multi_warehouse'] = array('2','3') : '';
        isset($hazmat_status) && ($hazmat_status == "1") ? $features['hazardous_material'] = array('2','3') : '';
    }
    
    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
//    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : $features[$feature];
}

add_filter('cerasis_freights_plans_notification_link' , 'cerasis_freights_plans_notification_link' , 1);

function cerasis_freights_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan){
        case 2:
            $plan_to_upgrade = "<a target='_blank' href='http://eniture.com/plan/woocommerce-cerasis-ltl-freight/'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a target='_blank' href='http://eniture.com/plan/woocommerce-cerasis-ltl-freight/'>Advanced Plan required</a>";
            break;
    }
    
    return $plan_to_upgrade;
}
/**
*
* old customer check dropship / warehouse status on plugin update
*/
function old_store_cerasis_ltl_dropship_status()
{
    global $wpdb;
    
//  Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);
    
    if(get_option('en_old_user_dropship_status') == "0" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
        $dropship_status = ($num > 1) ? 1 : 0 ;
        
        update_option('en_old_user_dropship_status' , "$dropship_status");        
    }
    elseif(get_option('en_old_user_dropship_status') == "" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
        $dropship_status = ($num == 1) ? 0 : 1 ;
 
        update_option('en_old_user_dropship_status' , "$dropship_status");        
    }

//  Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);
    
    if(get_option('en_old_user_warehouse_status') == "0" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
        $warehouse_status = ($warehouse_num > 1) ? 1 : 0 ;
        
        update_option('en_old_user_warehouse_status' , "$warehouse_status");        
    }
    elseif(get_option('en_old_user_warehouse_status') == "" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1 ;
 
        update_option('en_old_user_warehouse_status' , "$warehouse_status");        
    }
    
    
}
/**
*
* old customer check hazmat status on plugin update
*/

function old_store_cerasis_ltl_hazmat_status()
{
    global $wpdb;
    
//  Check total no. of warehouses on plugin updation
    $results = $wpdb->get_results("SELECT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_hazardousmaterials%' AND meta_value = 'yes'
            "
        );
        
    if(get_option('en_old_user_hazmat_status') == "0" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
	    $hazmat_status = (count($results) > 0) ? 0 : 1 ;
	    update_option('en_old_user_hazmat_status', "$hazmat_status" );  
    }
    elseif(get_option('en_old_user_hazmat_status') == "" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
	    $hazmat_status = (count($results) == 0) ? 1 : 0 ;
	    update_option('en_old_user_hazmat_status', "$hazmat_status" );      
    }

}
