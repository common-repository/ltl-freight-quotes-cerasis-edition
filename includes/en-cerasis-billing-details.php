<?php
/**
 * WooCommerce - Cerasis Edition
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
/**
 * Getting address from WooCommerce Update Version/ Old Version  | update changes class for customer details
 */
class En_Cerasis_Billing_Details
{
    
    /**
     * wooCommerce version
     * @var int
     */
    public $WooVersion;
    
    /**
     * Customer Billing Details
     */
    function __construct()
    {
        if (!function_exists('get_plugins'))
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';
            $this->WooVersion = $plugin_folder[$plugin_file]['Version'];            
    }

    /**
     * Getting Customer's Billing Details
     * @return array
     */
    function billing_details()
    {
        $parms = array();
        switch ($this->WooVersion) {
            case ($this->WooVersion <= '2.7'):
                $parms['postcode']  = WC()->customer->get_postcode();
                $parms['state']     = WC()->customer->get_state();
                $parms['city']      = WC()->customer->get_city();
                $parms['country']   = WC()->customer->get_country();
                $parms['s_address'] = WC()->customer->get_address();
                break;
            case ($this->WooVersion >= '3.0'):
                $parms['postcode']  = WC()->customer->get_billing_postcode();
                $parms['state']     = WC()->customer->get_billing_state();
                $parms['city']      = WC()->customer->get_billing_city();
                $parms['country']   = WC()->customer->get_billing_country();
                $parms['s_address'] = WC()->customer->get_billing_address_1();
                break;
            default:
                break;
        }
        return $parms;
    }   
}