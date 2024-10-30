<?php

/**
 * FedEx Small Update Plan
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activate FedEx SMALL
 */
function en_cerasis_freight_activate_hit_to_update_plan()
{
    $domain = cerasis_get_domain();
    
    $index = 'ltl-freight-quotes-cerasis-edition/ltl-freight-quotes-cerasis-edition.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';    
    
    $plugin_dir_url = plugin_dir_url(__FILE__). 'en-hit-to-update-plan.php';

    $post_data = array(
                    'platform'      => 'wordpress',
                    'carrier'       => '47',
                    'store_url'     => $domain,
                    'webhook_url'   => $plugin_dir_url,
                    'plugin_version' => $plugin_version,
    );
    $url = "http://eniture.com/ws/web-hooks/subscription-plans/create-plugin-webhook.php?";
    $response = wp_remote_get($url,
        array(
            'method' => 'GET',
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'body' => $post_data,
        )
    );
    $output = wp_remote_retrieve_body($response);
    
    $response = json_decode($output , TRUE);
    
    
        $plan = $response['pakg_group'];
        $expire_day = $response['pakg_duration'];
        $expiry_date = $response['expiry_date'];
        $plan_type = $response["plan_type"];
        
        if($response["pakg_price"] == "0"){ $plan = "0"; }   
        
        update_option('cerasis_freight_package_expire_days' , "$expire_day");
        update_option('cerasis_freight_package_expire_date' , "$expiry_date");        
        update_option('cerasis_freight_package' , "$plan");
        update_option("cerasis_freight_quotes_store_type" , "$plan_type");
        
        en_check_cerasis_ltl_plan_on_product_detail();
    

}
function en_check_cerasis_ltl_plan_on_product_detail(){
    
    $hazardous_feature_PD = 1;
    $dropship_feature_PD = 1;
    
//  Hazardous Material
    if(get_option('cerasis_freight_quotes_store_type') == "1")
    {
        $hazardous_material = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'hazardous_material');
        if(!is_array($hazardous_material))
        {
            $hazardous_feature_PD = 1;
        }
        else 
        {
            $hazardous_feature_PD = 0;
        }
    }
    
//  Dropship
    if(get_option('cerasis_freight_quotes_store_type') == "1")
    {
        $action_dropship = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'multi_dropship');
        if(!is_array($action_dropship))
        {
            $dropship_feature_PD = 1;
        }
        else
        {
            $dropship_feature_PD = 0;
        }
    }
    if(get_option('en_old_user_hazmat_status') == "1" && get_option('cerasis_freight_quotes_store_type') == "0")
    {
	    $hazardous_feature_PD = 0;     
    }    
    update_option('eniture_plugin_15' , array('cerasis_freight_package' => array('plugin_name' => 'LTL Freight Quotes - Cerasis Edition' , 'multi_dropship' => $dropship_feature_PD  , 'hazardous_material' => $hazardous_feature_PD )));
}
/**
 * Deactivate FedEx SMALL
 */
function en_cerasis_freight_deactivate_hit_to_update_plan()
{
    delete_option('eniture_plugin_15');
    delete_option('cerasis_freight_package');
    delete_option('cerasis_freight_package_expire_days');
    delete_option('cerasis_freight_package_expire_date');    
    delete_option('cerasis_freight_quotes_store_type');    
}

/**
 * Get FedEx Small Plan
 * @return string
 */
function en_cerasis_freight_plan_name()
{
    $plan = get_option('cerasis_freight_package');
    $expire_days = get_option('cerasis_freight_package_expire_days');
    $expiry_date = get_option('cerasis_freight_package_expire_date');    
    switch ($plan)
    {
        case 3:
            $plan_name = "Advanced Plan";
            break;
        case 2:
            $plan_name = "Standard Plan";
            break;
        case 1:
            $plan_name = "Basic Plan";
            break;
        default:
            $plan_name = "Trial Plan";
    }
    $package_array = array(
        'plan_number' => $plan,
        'plan_name' => $plan_name,
        'expire_days' => $expire_days,
        'expiry_date' => $expiry_date
    );
    return $package_array;
}

/**
 * Show FedEx Small Plan Notice
 * @return string
 */
function en_cerasis_ltl_plan_notice() {
    if(isset($_GET['tab']) && ($_GET['tab'] == "cerasis_freights"))
    {
        $store_type = get_option('cerasis_freight_quotes_store_type');
        $plan_number = get_option("cerasis_freight_package");        
        $plan_package = en_cerasis_freight_plan_name();
        
      if($store_type == "1" || $store_type == "0" && ($plan_number == "0" || $plan_number == "1" || $plan_number == "2" || $plan_number == "3"))
      {
            if(isset($plan_package) && !empty($plan_package))
            {

                if($plan_package['plan_number'] == '0')
                {

                    echo '<div class="notice notice-success is-dismissible">
                    <p> You are currently on the '.$plan_package['plan_name'].'. Your plan will be expire within '.$plan_package['expire_days'].' days and plan renews on '.$plan_package['expiry_date'].'.</p>
                    </div>';
                }
                else if( $plan_package['plan_number'] == '1' || $plan_package['plan_number'] == '2' || $plan_package['plan_number'] == '3')
                {

                    echo '<div class="notice notice-success is-dismissible">
                    <p> You are currently on the '.$plan_package['plan_name'].'. The plan renews on '.$plan_package['expiry_date'].'.</p>
                    </div>';
                } 
                else 
                {
                    echo '<div class="notice notice-warning is-dismissible">
                    <p>Your currently plan subscription is inactive. Please activate your plan subscription from <a target="_blank" href="http://eniture.com/plan/woocommerce-cerasis-ltl-freight/">here</a>.</p>
                    </div>';
                }
            } 
      }  
        
    }
}

add_action( 'admin_notices', 'en_cerasis_ltl_plan_notice' );
