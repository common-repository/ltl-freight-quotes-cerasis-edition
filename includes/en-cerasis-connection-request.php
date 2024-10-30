<?php
/**
 * Connection Request Class | getting connection
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Connection Request Class | getting connection with server
 */
class En_Cerasis_Connection_Request
{

    /**
     * cerasis connection request class constructor
     */
    function __construct()
    {
        add_action('wp_ajax_nopriv_test_connection_call',
            array($this, 'cerasis_test_connection'));
        add_action('wp_ajax_test_connection_call',
            array($this, 'cerasis_test_connection'));
    }

    /**
     * cerasis test connection function
     * @param none
     * @return array
     */
    function cerasis_test_connection()
    {
        if (isset($_POST)) {

            foreach ($_POST as $key => $post) {
                $data[$key] = sanitize_text_field($post);
            }

            $shippingID = $data['wc_cerasis_shipper_id'];
            $username = $data['wc_cerasis_username'];
            $password = $data['wc_cerasis_password'];
            $accessKey = $data['authentication_key'];
            $license_key = $data['wc_cerasis_licence_key'];

            $domain = cerasis_get_domain();
            $data = array(
                'license_key' => $license_key,
                'server_name' => $domain,
                'carrierName' => 'cerasis',
                'platform' => 'WordPress',
                'carrier_mode' => 'test',
                // -------------Carrier Credentials------------- //
                'shipperID' => $shippingID,
                'username' => $username,
                'password' => $password,
                'accessKey' => $accessKey,
            );
        }
        
        $cerasis_curl_obj   = new En_Cerasis_Curl_Class();
        $sResponseData      = $cerasis_curl_obj->get_curl_response('https://eniture.com/ws/index.php', $data);
        $output_decoded     = json_decode($sResponseData);
                    
        
        if (empty($output_decoded)) {
            $re['error'] = 'We are unable to test connection. Please try again later.';
        }
        if (isset($output_decoded->severity) && $output_decoded->severity == 'SUCCESS') {

            $re['success'] = $output_decoded->Message;
        }
        else if (isset($output_decoded->severity) && $output_decoded->severity == 'ERROR') {
            $re['error'] = $output_decoded->Message;
        }
        else {

            $re['error'] = $output_decoded->error;
        }
        echo json_encode($re);
        exit();
    }
}
