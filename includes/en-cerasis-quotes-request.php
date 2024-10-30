<?php

/**
 * Quote Request Class | quote request for getting carriers
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quote Request Class | getting request for cart items, sending request  
 */
class En_Cerasis_Quotes_Request extends En_Cerasis_Cart_To_Request {
    
    /**
    * details array
    * @var array type 
    */
    public $quote_settings;
    public $instorepickup;
    public $en_wd_origin_array;
    /**
     * Quote Request constructor
     */
    function __construct() { 
    }
    /**
     * Quotes Request
     * @param $packages
     * @return array 
     */
    public function quotes_request($packages , $package_plugin='')
    {   
        if( !empty($packages) )
         
        $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");
    
        $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : array();
        
        $domain = cerasis_get_domain();
        
        $post_data = array(
            'apiVersion'        => $this->apiVersion,
            'plateform'         => $this->plateform,
            'carrierName'       => $this->carrier_name,
            'requestKey'        => md5(microtime().rand()),
            
            'suspend_residential'       => get_option('suspend_automatic_detection_of_residential_addresses'),
            'residential_detecion_flag' => $residential_detecion_flag,
            
            'carriers'          => array(
                'cerasis'           => array(
                    'licenseKey'                    => get_option('wc_settings_cerasis_licence_key'),
                    'serverName'                    => $_SERVER['SERVER_NAME'],
                    'serverName'                    => $domain,
                    'carrierMode'                   => $this->carrierMode,
                    'quotestType'                   => $this->quotestType,
                    'version'                       => $this->version(),
                    'returnQuotesOnExceedWeight'    => $this->quotes_on_exceed_weight(),
                    'api'                           => $this->api_credentials($packages),
                    'originAddress'                 => $this->origin_address($packages)
                )
            ),
            'receiverAddress'   => $this->reciever_address(),
            'commdityDetails'   => $this->line_items($packages['items'])
        ); 
        
        $En_Cerasis_Liftgate_As_Option = new En_Cerasis_Liftgate_As_Option();
        $post_data = $En_Cerasis_Liftgate_As_Option->cerasis_freights_update_carrier_service($post_data);
        $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request" , $post_data , en_woo_plugin_cerasis_freights);
        
//      In-store pickup and local delivery
        $instore_pickup_local_devlivery_action = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'instore_pickup_local_devlivery');
        if(!is_array($instore_pickup_local_devlivery_action)){
            $post_data['carriers']['cerasis']['api']['InstorPickupLocalDelivery'] = apply_filters('en_wd_standard_plans' , $post_data , $post_data['receiverAddress']['receiverZip'] , $this->en_wd_origin_array , $package_plugin);
        }
//      Eniture debug mood
        do_action("eniture_debug_mood" , "Plugin Features(Cerasis) " , get_option('eniture_plugin_15'));
        do_action("eniture_debug_mood" , "Quotes Request (Cerasis)" , $post_data);
     
        
        return $post_data;
    }

    /**
     * Getting Line Items
     * @param $packages
     * @return array
     */
    function line_items($packages)
    {
        
        $line_item      = array();
		$hazmat_flage = false;
		if(get_option('cerasis_freight_quotes_store_type') == "1")
        {
            $hazardous_material = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'hazardous_material');
			if(!is_array($hazardous_material))
            {
				$hazmat_flage = true;
			}
		}
		else
		{
			$hazmat_status = get_option('en_old_user_hazmat_status');
			isset($hazmat_status) && ($hazmat_status == 1) ? $hazmat_flage = false : $hazmat_flage = true;
		}
        foreach ($packages as $item) {
            $line_item[] = array(
                'freightClass'      => $item['freightClass'],
                'lineItemHeight'    => $item['productHeight'],
                'lineItemLength'    => $item['productLength'],
                'lineItemWidth'     => $item['productWidth'],
                'lineItemClass'     => $item['productClass'],
                'lineItemWeight'    => $item['productWeight'],
                'piecesOfLineItem'  => $item['productQty'],
                'isHazmatLineItem'  => isset($hazmat_flage) && ($hazmat_flage == true) ? $item['isHazmatLineItem'] : 'N'
            );
        }
        return $line_item;
    }

    /**
     * Checking item is hazmet or not
     * @param $packages
     * @return string
     */
    function item_hazmet($packages) {
        foreach ($packages['items'] as $item):
            $items_id[] = array(
                'id' => $item['productId']
            );
        endforeach;
        foreach ($items_id as $pid):
            $enable_hazmet[] = get_post_meta($pid['id'], '_hazardousmaterials', true);
        endforeach;
        $hazmet = 'N';
        if(get_option('cerasis_freight_quotes_store_type') == "1")
        {
            $hazardous_material = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'hazardous_material');
            if(!is_array($hazardous_material))
            {
                if (in_array("yes", $enable_hazmet)) {
                $hazmet = 'Y';
                } else {
                    $hazmet = 'N';
                }                
            }
        }
        else
        {
            if (in_array("yes", $enable_hazmet)) {
            $hazmet = 'Y';
            } else {
                $hazmet = 'N';
            }
        }
        return $hazmet;
    }

    /**
     * Checking item delivery is Residential/Liftgate
     * @return array
     */
    function item_accessorial()
    {   
        $accessorials       = array();
        $wc_liftgate        = get_option('wc_settings_cerasis_lift_gate_delivery');
        $wc_offer_liftgate  = get_option('cerasis_freights_liftgate_delivery_as_option');
        $wc_residential     = get_option('wc_settings_cerasis_residential_delivery');
        ($wc_liftgate == 'yes') ?  $accessorials[] = 'LFTGATORIG' : "";
        ($wc_offer_liftgate == 'yes') ?  $accessorials[] = 'LFTGATORIG' : "";
        ($wc_residential == 'yes') ?  $accessorials[] = 'RESFURDEL' : "";
        return $accessorials;
        
    }

    /**
     * Getting origin address
     * @param $packages
     * @return array
     */
    function origin_address($packages)
    {   
        foreach($packages['origin'] as $k => $origin):
            
           
            $origin['senderZip'] = preg_replace('/\s+/', '', $origin['zip']);

            if(trim($origin['country']) == 'USA') {
                $origin['country'] = 'US';
            }

            $origin_address[] = array(
                'location_id'       => $origin['locationId'],
                'senderCity'        => $origin['city'],
                'senderState'       => $origin['state'],
                'senderZip'         => $origin['zip'],
                'location'          => $origin['location'],
                'senderCountryCode' => trim($origin['country'])//$this->origin_country($origin['senderCountryCode']),
            );
        endforeach;
        
         
        return $origin_address;
    }

    /**
     * Country Code
     * @param $country
     */
    function origin_country($country)
    {
        if (isset($country)) {
            if ($country = 'US' || $country = 'USA') {
                $sender_country = "US";
            } else if ($country = 'CA' || $country = 'CN' || $country = 'CAN') {
                $sender_country = 'CA';
            }
        }
        return $sender_country;
    }

    /**
     * Getting customer address
     * @return array
     */
    function reciever_address()
    {
        $billing_obj        = new En_Cerasis_Billing_Details();
        $billing_details        = $billing_obj->billing_details();
        $freight_zipcode    = "";
        $freight_state      = "";
        $freight_city       = "N";
        
       (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = $billing_details['postcode'];
        (strlen(WC()->customer->get_shipping_state()) > 0) ? $freight_state = WC()->customer->get_shipping_state() : $freight_state = $billing_details['state'];
        (strlen(WC()->customer->get_shipping_country()) > 0) ? $freight_country = WC()->customer->get_shipping_country() : $freight_country = $billing_details['country'];
        (strlen(WC()->customer->get_shipping_city()) > 0) ? $freight_city = WC()->customer->get_shipping_city() : $freight_city = $billing_details['city'];
        (strlen(WC()->customer->get_shipping_address_1()) > 0) ? $freight_addressline = WC()->customer->get_shipping_address_1() : $freight_addressline = $billing_details['s_address'];
        
        if(trim($freight_country)== 'USA') {
                $freight_country = 'US';
            }
        $freight_zipcode= preg_replace('/\s+/', '', $freight_zipcode);
        $address = array(
            'receiverCity'          => $freight_city,
            'receiverState'         => $freight_state,
            'receiverZip'           => $freight_zipcode,
            'receiverCountryCode'   => trim($freight_country),//$this->origin_country($freight_country),
            'addressLine'           => (isset($_POST['s_addres'])) ? trim($_POST['s_addres']) : $freight_addressline
        );
        return $address;
    }

    /**
     * API Credentials
     * @param $packages
     * @return array/string
     */
    function api_credentials($packages) {
        $credentials = array(
            'shipperID'     => get_option('wc_settings_cerasis_shipper_id'),
            'username'      => get_option('wc_settings_cerasis_username'),
            'password'      => get_option('wc_settings_cerasis_password'),
            'accessKey'     => get_option('wc_settings_cerasis_authentication_key'),
            'direction'     => 'Dropship',
            'billingType'   => 'Prepaid',
            'hazmat'        => $this->item_hazmet($packages),
            'accessorial'   => $this->item_accessorial(),
        );
        return $credentials;
    }
    /**
     * Quotes On Exceed Weight
     * @return int
     */
    function quotes_on_exceed_weight(){
        if(get_option('en_plugins_return_LTL_quotes') == 'yes'){
            $quotes_on_exceed_weight = "1";
        }else{
            $quotes_on_exceed_weight = "0";
        }
        return $quotes_on_exceed_weight;
    }
    
    /**
     * getting quotes via curl class
     * @param $request_data
     * @return string
     */
    function get_quotes($request_data)
    {
        
//      check response from session 
        $srequest_data = $request_data;
        $srequest_data['requestKey'] = "";
        $currentData = md5(json_encode($srequest_data));

        $requestFromSession = WC()->session->get( 'previousRequestData' );

        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

        if(isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData])))
        {
            
//          Eniture debug mood
            do_action("eniture_debug_mood" , "Build Query (CERASIS)" , http_build_query($request_data));
            
//          Eniture debug mood
            do_action("eniture_debug_mood" , "Quotes Response (CERASIS)" , json_decode($requestFromSession[$currentData]));
                        
            $quote_response = json_decode($requestFromSession[$currentData]);
            $instorepickup_resp = (reset($quote_response));
            $instorepickup_resp = (reset($instorepickup_resp));
            
            $this->instorepickup = isset($instorepickup_resp->InstorPickupLocalDelivery) && !empty($instorepickup_resp->InstorPickupLocalDelivery) ? $instorepickup_resp->InstorPickupLocalDelivery: array();
            if (isset($quote_response->cerasis) && !empty($quote_response->cerasis)) {
                return $quote_response->cerasis;
            }
            return FALSE;
        }
                    
        if (is_array($request_data) && count($request_data) > 0) {
            $curl_obj       = new En_Cerasis_Curl_Class();
            $request_data['requestKey'] = md5(microtime().rand());
            $output         = $curl_obj->get_curl_response($this->end_point_url_pro, $request_data);
          
//          set response in session
            $response = isset($output) && !empty($output) ? json_decode($output , TRUE) : array();

            if(isset($response['cerasis']) && (!empty($response['cerasis']))) 
            {    
                
                $cerasis_reset = reset($response['cerasis']);

                if(isset($cerasis_reset['autoResidentialSubscriptionExpired']) && 
                                ($cerasis_reset['autoResidentialSubscriptionExpired'] == 1))
                {
                    $flag_api_response = "no";
                    $srequest_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($srequest_data));
                }
                
                if(!isset($cerasis_reset['severity']) || (isset($cerasis_reset['severity']) && ($cerasis_reset['severity'] != "ERROR")))
                {
                    $requestFromSession[$currentData] = $output;      
                    WC()->session->set( 'previousRequestData', $requestFromSession );
                }
            }
            
//          Eniture debug mood
            do_action("eniture_debug_mood" , "Quotes Response (CERASIS)" , json_decode($output));
            
            $quote_response = json_decode($output);
            
            
            $instorepickup_resp = (reset($quote_response));
            $instorepickup_resp = (reset($instorepickup_resp));
            
            $this->instorepickup = isset($instorepickup_resp->InstorPickupLocalDelivery) && !empty($instorepickup_resp->InstorPickupLocalDelivery) ? $instorepickup_resp->InstorPickupLocalDelivery: array();
            
            if (isset($quote_response->cerasis) && !empty($quote_response->cerasis)) {
                return $quote_response->cerasis;
            }
            return FALSE;
        }
    }
    public function return_cerasis_localdelivery_array()
    {
        return $this->instorepickup;
    }
    /**
    * check "R" in array
    * @param array type $label_sufex
    * @return array type
    */
    public function label_R_cerasis($label_sufex)
    {
        if(get_option('wc_settings_cerasis_residential_delivery') == 'yes' && (in_array("R", $label_sufex)))
        {
            $label_sufex = array_flip($label_sufex);
            unset($label_sufex['R']);
            $label_sufex = array_keys($label_sufex);

        }

        return $label_sufex;
    }
    
    /**
     * passing quotes result to display
     * @param $quotes
     * @param $cart_obj
     * @param $handlng_fee
     * @return string/array
     */
    function pass_quotes($quotes, $cart_obj, $handlng_fee)
    {   
        $carr = $this->get_active_carriers();

        $allServices = array();
        if(isset( $quotes )){
            $En_Cerasis_Liftgate_As_Option = new En_Cerasis_Liftgate_As_Option();
            $label_sufex = $En_Cerasis_Liftgate_As_Option->filter_label_sufex_array_cerasis_freights($quotes);
            
            $count = 0;
            $price_sorted_key = array();
            $simple_quotes    = array();
            $quotes = (isset($quotes['q'])) ? $quotes['q'] : array();

            if(!empty($quotes)) {

                foreach ($quotes as $quote) {

    //                if (in_array($quote->serviceType, $carr)) {
                    if (isset($carr[$quote['serviceType']])) {
                        
                        $allServices[$count] = array(
                            'id'  => $quote['serviceType'],
                            'carrier_scac'  => $quote['serviceType'],
                            'carrier_name'  => $quote['serviceDesc'],
                            'label'         => $quote['serviceDesc'],
                            'label_sufex'   => $label_sufex,
                            'cost'          => $quote['totalNetCharge'],
                            'transit_days'  => $quote['deliveryDayOfWeek']
                        );
                        
                        $allServices[$count] = apply_filters("en_woo_addons_web_quotes" , $allServices[$count] , en_woo_plugin_cerasis_freights);

                        $label_sufex = (isset($allServices[$count]['label_sufex'])) ? $allServices[$count]['label_sufex'] : array();  
                        
                        $label_sufex = $this->label_R_cerasis($label_sufex);
                        $allServices[$count]['label_sufex'] = $label_sufex;
                        
                        $liftgate_charge = (isset($carr[$quote['serviceType']])) ? $carr[$quote['serviceType']] : 0 ;

                        if(($this->quote_settings['liftgate_delivery_option'] == "yes") && array_filter($carr) &&
                                        (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) || 
                                        ($this->quote_settings['liftgate_resid_delivery'] != "yes")))
                        {
                            if($liftgate_charge > 0)
                            {
                                $service = $allServices[$count];
                                (isset($allServices[$count]['id'])) ? $allServices[$count]['id'] .= "WL" : $allServices[$count]['id'] = "WL";

                                (isset($allServices[$count]['label_sufex']) && 
                                        (!empty($allServices[$count]['label_sufex']))) ? 
                                            array_push($allServices[$count]['label_sufex'], "L") :  // IF
                                            $allServices[$count]['label_sufex'] = array("L");       // ELSE
    //
                                $allServices[$count]['append_label'] = " with lift gate delivery ";

                                $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                                (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = array();
    //                            $service['append_label'] = " with lift gate delivery ";
    //                            $service['label_sufex'] = array("L");

                                $simple_quotes[$count] = $service;

                                $price_sorted_key[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;
                            } 
                            else 
                            {
                                if(isset($allServices[$count])) unset ($allServices[$count]);
                            }
                        }
                        
                        $count++;
                    }
                }
            }
        }
        
//          ===================================== array_multisort =======================================
            (!empty($simple_quotes)) ? array_multisort($price_sorted_key, SORT_ASC, $simple_quotes) : "";
//          =============================================================================================
            
            (!empty($simple_quotes)) ? $allServices['simple_quotes'] = $simple_quotes : "";

        return $allServices;
    }
    
    function destinationAddressCerasis()
    {
        $cerasis_woo_obj = new Cerasis_Woo_Update_Changes();

        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $cerasis_woo_obj->cerasis_postcode();
        $freight_state   = (strlen(WC()->customer->get_shipping_state()) > 0)    ? WC()->customer->get_shipping_state()    : $cerasis_woo_obj->cerasis_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0)  ? WC()->customer->get_shipping_country()  : $cerasis_woo_obj->cerasis_getCountry();
        $freight_city    = (strlen(WC()->customer->get_shipping_city()) > 0)     ? WC()->customer->get_shipping_city()     : $cerasis_woo_obj->cerasis_getCity();
        $address         = (strlen(WC()->customer->get_address()) > 0)     ? WC()->customer->get_address()     : $cerasis_woo_obj->cerasis_getAddress1();
        return array(
            'city'      => $freight_city , 
            'state'     => $freight_state , 
            'zip'       => $freight_zipcode , 
            'country'   => $freight_country,
            'address'   => $address,
        );
    }

    /**
     * getting warehouse address
     * @param $warehous_list
     * @param $receiver_zip_code
     * @return array
     */
    public function get_warehouse($warehous_list, $receiver_zip_code)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->cerasis_origin_array($warehous_list);
        }
        
        $cerasis_distance_request  = new Get_cerasis_freight_distance();
        $accessLevel    = "MultiDistance";
        $response_json  = $cerasis_distance_request->cerasis_freight_address($warehous_list, $accessLevel, $this->destinationAddressCerasis());
        $response_json  = json_decode($response_json);

        return $this->cerasis_origin_array( $response_json->origin_with_min_dist );
    }

    /**
     * getting plugin origin
     * @param $origin
     * @return array
     */
    function cerasis_origin_array($origin) {
        
//      In-store pickup and local delivery
        if(has_filter("en_wd_origin_array_set"))
        {
            return  apply_filters("en_wd_origin_array_set" , $origin);
        }
        
        $origin_array = array(
            'location_id'       => $origin->id,
            'senderZip'         => $origin->zip,
            'senderCity'        => $origin->city,
            'senderState'       => $origin->state,
            'location'          => $origin->location,
            'senderCountryCode' => $origin->country
        );
        return $origin_array;
    }

    /**
     * All Enabled Carriers List
     * @global $wpdb
     * @return array
     */
    function get_active_carriers() {
        global $wpdb;
        $all_carriers = $wpdb->get_results(
                "SELECT `id`, `carrier_scac`, `carrier_status`, `liftgate_fee` FROM " . $wpdb->prefix . "carriers WHERE `carrier_status`='1'"
        );
        if ($all_carriers) {
            foreach ($all_carriers as $key => $value) {
//                $carriers[] = $value->carrier_scac;
                $carriers[$value->carrier_scac] = $value->liftgate_fee;
            }
            return $carriers;
        } else {
            return $carriers = array('Error' => 'Not active carriers found!');
        }
    }
    
}
