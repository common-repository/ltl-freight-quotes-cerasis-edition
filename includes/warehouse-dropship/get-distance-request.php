<?php

/**
 * WWE Small Get Distance
 * 
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Distance Request Class
 */
class Get_cerasis_freight_distance {
    
    function __construct() 
    {
        add_filter("en_wd_get_address" , array($this , "sm_address"), 10, 2);
    }

    /**
     * Get Address Upon Access Level
     * @param $map_address
     * @param $accessLevel
     */
    function cerasis_freight_address($map_address, $accessLevel, $destinationZip = array()) {

        $domain = cerasis_get_domain();
        $postData = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses'   => (isset($map_address)) ? $map_address : "",
            'destinationAddress'=> (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('wc_settings_cerasis_licence_key'),
            'ServerName' => $domain,
        );
        $cerasis_Curl_Request = new En_Cerasis_Curl_Class();
        $output       = $cerasis_Curl_Request->get_curl_response('https://eniture.com/ws/addon/google-location.php', $postData);    
        return $output;
    }

}
