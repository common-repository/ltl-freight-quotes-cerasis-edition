<?php

/**
 * Woocommerce Cerasis Curl Class
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Curl Request Class | getting curl response
 */
class En_Cerasis_Curl_Class {

    /**
     * Get Curl Response 
     * @param $url
     * @param $post_data
     * @return json/array
     */
    
    
    
    function get_curl_response($url, $post_data) {
        
        
        if (!empty($url) && !empty($post_data)) {
            $field_string = http_build_query($post_data);
            
//          Eniture debug mood
            do_action("eniture_debug_mood" , "Build Query" , $field_string);
            
            $response = wp_remote_post($url,
                array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );      
            $output = wp_remote_retrieve_body($response);            
            $response = json_decode($output , TRUE); 
            
            if (empty($response)) {
                return $response = json_encode(array('error' => 'Unable to get response from API')) ;
            }
            
            return $output;
        }
    }
}
