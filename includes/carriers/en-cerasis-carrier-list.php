<?php
/**
 * Cerasis Carriers List Class | saving carrier list to database when plugin called for activation
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
/**
 * Carriers List Class | saving carriers list to database when plugin called for activation
 */
class En_Cerasis_Carrier_List
{
    /**
     * carrier list class constructor
     */
    function __construct()
    {
        add_action('wp_ajax_nopriv_refresh_carriers', array($this, 'carriers'));
        add_action('wp_ajax_refresh_carriers', array($this, 'carriers'));
        
        add_action('wp_ajax_nopriv_auto_enable_action', array($this, 'auto_enable_action'));
        add_action('wp_ajax_auto_enable_action', array($this, 'auto_enable_action'));
    }
    
    function auto_enable_action()
    {
        $auto_enable = (isset($_POST['auto_enable'])) ? $_POST['auto_enable'] : "";
        update_option("automatically_enable_new_carriers", $auto_enable);
    }

    /**
    * If array_columsn not exists.
    * @param array $input
    * @param type $columnKey
    * @param type $indexKey
    * @return boolean|array
    */
    function array_column_fun(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if (!array_key_exists($columnKey, $value)) {

                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!array_key_exists($indexKey, $value)) {

                    return false;
                }
                if (!is_scalar($value[$indexKey])) {

                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
    
    /**
     * carriers names, code, and logo
     * @global $wpdb
     */
    function carriers() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $table_name = $wpdb->prefix . "carriers";

        $db_carriers = $wpdb->get_results("SELECT `carrier_scac` FROM ".$table_name." WHERE `plugin_name` = 'cerasis_ltl_carriers'");
        
        if(isset($db_carriers) && !empty($db_carriers))
        {
            $db_carriers = json_decode(json_encode($db_carriers) , TRUE);
            
            if (!function_exists('array_column')) 
            {
                $carrier_scac = $this->array_column_fun($db_carriers, 'carrier_scac');
            } 
            else 
            {
                $carrier_scac = array_column($db_carriers, 'carrier_scac');
            }
        }
        else
        {
            $carrier_scac = array();
        }

        $data = array(
            'licence_key'   => get_option('wc_settings_cerasis_licence_key'),
            'server_name'   => cerasis_get_domain(),
            'platform'      => 'WordPress',
            'carrierName'   => 'cerasis',
            'carrier_mode'  => 'getcarriers', 

            'requestKey'    => '11461611123446',
            
            // -------------Carrier Credentials------------- //
            'shipperID'     => get_option('wc_settings_cerasis_shipper_id'),
            'username'      => get_option('wc_settings_cerasis_username'),
            'password'      => get_option('wc_settings_cerasis_password'),
            'accessKey'     => get_option('wc_settings_cerasis_authentication_key'),
        );
        
        $En_Cerasis_Curl_Class = new En_Cerasis_Curl_Class();
                $carriers = $En_Cerasis_Curl_Class->get_curl_response("https://eniture.com/ws/index.php" , $data);
        $carriers = json_decode($carriers);

        if($carriers = isset($carriers->carriers) && !empty($carriers->carriers) ? $carriers->carriers : array())
        {
            foreach ($carriers as $key => $carrier) 
            {
                $CarrierSCAC = (isset($carrier->CarrierSCAC)) ? $carrier->CarrierSCAC : "";
                if(!in_array($CarrierSCAC, $carrier_scac))
                {
                    $auto_enable = get_option('automatically_enable_new_carriers') == "yes" ? 1 : 0;
                    $wpdb->insert(
                        $table_name, array(
                        'carrier_scac'  => $CarrierSCAC,
                        'carrier_name'  => (isset($carrier->CarrierName)) ? $carrier->CarrierName : "",
                        'carrier_logo'  => (isset($carrier->CarrierLogoUrl)) ? $carrier->CarrierLogoUrl : "",
                        'plugin_name'   => 'cerasis_ltl_carriers',
                        'carrier_status'   => $auto_enable
                    ));
                }
            }

                $date = date('m/d/Y h:i:s a', time());
                update_option('carriers_updated_time', '('.$date.')');
        }
        
    }
}

new En_Cerasis_Carrier_List();
