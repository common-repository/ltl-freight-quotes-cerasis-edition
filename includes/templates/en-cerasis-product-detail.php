<?php
/**
 * Drop Ship For Shipping Section In Product Detail Page
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

if( ! has_filter( 'En_Plugins_freight_classification_filter' ) ){
    add_action( 'woocommerce_product_options_shipping', 'en_cerasis_ltl_freight_class', 10 );
    add_filter( 'En_Plugins_freight_classification_filter', 'en_cerasis_ltl_freight_class_filter' , 10, 1 );
}
/**
 * Product LTL Freight Classification
 * @param $freight_clasification
 * @return string
 */
function en_cerasis_ltl_freight_class_filter( $freight_clasification ) {
    return $freight_clasification;
}
/**
 * cerasis ltl frieght class
 * @return string
 */
function en_cerasis_ltl_freight_class() {
    $classes = en_cerasis_ltl_freight_class_array();
    $freight_clasification = woocommerce_wp_select(
        array(
            'id' => '_ltl_freight',
            'label' => __('Freight classification', 'woocommerce'),
            'options' => $classes
        )
    );	
    apply_filters( 'En_Plugins_freight_classification_filter', $freight_clasification );
}

/**
 * cerasis ltl frieght class array
 * @return array
 */
function en_cerasis_ltl_freight_class_array() {
    $classification = array(
            '0' => __('No Freight Class', 'woocommerce'),
            '50' => __('50', 'woocommerce'),
            '55' => __('55', 'woocommerce'),
            '60' => __('60', 'woocommerce'),
            '65' => __('65', 'woocommerce'),
            '70' => __('70', 'woocommerce'),
            '77.5' => __('77.5', 'woocommerce'),
            '85' => __('85', 'woocommerce'),
            '92.5' => __('92.5', 'woocommerce'),
            '100' => __('100', 'woocommerce'),
            '110' => __('110', 'woocommerce'),
            '125' => __('125', 'woocommerce'),
            '150' => __('150', 'woocommerce'),
            '175' => __('175', 'woocommerce'),
            '200' => __('200', 'woocommerce'),
            '225' => __('225', 'woocommerce'),
            '250' => __('250', 'woocommerce'),
            '300' => __('300', 'woocommerce'),
            '400' => __('400', 'woocommerce'),
            '500' => __('500', 'woocommerce'),
            'DensityBased' => __('Density Based', 'woocommerce')
            );
    return $classification;
}
        
if( ! has_filter( 'En_Plugins_variable_freight_classification_filter' ) ){
    add_action( 'woocommerce_product_after_variable_attributes', 'en_cerasis_ltl_variable_fields', 20, 3 );
    add_filter( 'En_Plugins_variable_freight_classification_filter', 'en_cerasis_ltl_freight_class_filter', 20, 1 );
}
/**
 * Freight Classification For Shipping Section In Product Detail Page ( Variation Products )
 * @param $freight_clasification
 * @return string
 */
function en_cerasis_ltl_variable_freight_class_filter( $freight_clasification ) {
    return $freight_clasification;
}

/**
 * Drop Ship For Shipping Section In Product Detail Page
 * @global $wpdb
 * @param $loop
 * @param $variation_data
 * @param $variation
 * @return array
 */
function en_cerasis_ltl_dropship($loop, $variation_data = array(), $variation = array()) {
    global $wpdb;
    $dropship_list = $wpdb->get_results("SELECT id, city, state, zip, country, location, nickname FROM ".$wpdb->prefix."warehouse WHERE location = 'dropship'");
    if(!empty($dropship_list)){

        ( isset( $variation->ID ) && $variation->ID > 0 ) ? $variationID = $variation->ID : $variationID = get_the_ID();

        /*
         * create enable dropship checkbox.
         */

        woocommerce_wp_checkbox(
        array(
            'id'    => '_enable_dropship['.$variationID.']',
            'label' => __('Enable drop ship location', 'woocommerce'),
            'value' => get_post_meta( $variationID, '_enable_dropship', true ),
            )
        );

        $attributes = array(
                'id' => '_dropship_location['.$variationID.']',
                'class' => 'p_ds_location',
        );

        $get_loc = maybe_unserialize(get_post_meta($variationID, '_dropship_location', true));

        $valuesArr = array();
        foreach ($dropship_list as $list) {
            ( isset($list->nickname) && $list->nickname == '' ) ? $nickname = '' : $nickname = $list->nickname . ' - ';
            ( isset($list->country) && $list->country == '' ) ? $country = '' : $country = '(' .$list->country. ')';
            $location = $nickname . $list->zip . ', ' . $list->city . ', ' . $list->state .' ' . $country;
            $finalValue['option_id'] = $list->id;
            $finalValue['option_value'] = $list->id;
            $finalValue['option_label'] = $location;
            $valuesArr[] = $finalValue;
        }

        $aFields[] = array(
                'attributes'    =>  $attributes,
                'label'         => 'Drop ship location',
                'value'         => $valuesArr,
                'name'          => '_dropship_location['.$variationID.'][]',
                'type'          =>'select',
                'selected_value' => $get_loc,
                'variant_id'    => $variationID
            );

        $aFields = apply_filters('before_cerasis_ltl_product_detail_fields', $aFields);
        
        apply_filters('En_Plugins_dropship_filter', $aFields,$get_loc, $variationID);
    }
}
        
    
if( ! has_filter( 'En_Plugins_dropship_filter' ) ) {
    add_action( 'woocommerce_product_options_shipping', 'en_cerasis_ltl_dropship' );
    add_action( 'woocommerce_product_after_variable_attributes', 'en_cerasis_ltl_dropship', 10, 3 );
    add_filter( 'En_Plugins_dropship_filter','en_cerasis_ltl_dropship_filter', 10, 3 );

}

/**
 * Dropship Filters
 * @param $aFields
 * @param $get_loc
 * @param $variationID
 */
function en_cerasis_ltl_dropship_filter( $aFields, $get_loc, $variationID ) {
    $fieldsHtml = '';
    foreach ($aFields as $key => $sField) {
        $sField = apply_filters( 'cerasis_ltl_product_detail_fields', $sField );
        $fieldsHtml = en_cerasis_ltl_dropship_html($sField, $fieldsHtml, $get_loc, $variationID);
    }
    $fieldsHtml = apply_filters('after_cerasis_ltl_product_detail_fields', $fieldsHtml);
    echo $fieldsHtml;
}

/**
 * Attribute For Drop Ship Dropdown
 * @param $attributes
 * @return string
 */
function en_cerasis_ltl_attributes_string( $attributes ) {
    $str = '';
    foreach($attributes as $key=>$sAttribute){
        $str .= ' '.$key.' ="'.$sAttribute.'" ';                                        
    }
    return $str;
}
    

/**
 * Drop Ship Dropdown Select
 * @param $sField
 * @param $fieldsHtml
 * @return string
 */
function en_cerasis_ltl_dropship_html( $sField, $fieldsHtml ) {
    
    $description = "";
    $disable_me = FALSE;

    $plan_notifi = apply_filters('en_woo_plans_notification_action' , array());

    $dropship_flag = count($sField['value']);
    
    $dropship_flag = isset($dropship_flag) && ($dropship_flag>1) ? true : false;    
    
    if(!empty($plan_notifi) && (isset($plan_notifi['multi_dropship'])))
    {
        $enable_plugins = (isset($plan_notifi['multi_dropship']['enable_plugins'])) ? $plan_notifi['multi_dropship']['enable_plugins'] : "";
        $disable_plugins = (isset($plan_notifi['multi_dropship']['disable_plugins'])) ? $plan_notifi['multi_dropship']['disable_plugins'] : "";
        if(strlen($disable_plugins) > 0)
        {
            if(strlen($enable_plugins) > 0)
            {
                $description =  "<br><br>" . apply_filters('en_woo_plans_notification_message_action' , $enable_plugins , $disable_plugins);
            }
            else
            {
                if($dropship_flag && get_option('cerasis_freight_quotes_store_type') == "1")
                {
                    $description = apply_filters('cerasis_freights_plans_notification_link' , array(2));
                    $disable_me = TRUE;
                }
                elseif(get_option('cerasis_freight_quotes_store_type') == "0"&& get_option('en_old_user_dropship_status') == "1")
                {
                    //old user and single dropship then show msg standard required
                    $description = apply_filters('cerasis_freights_plans_notification_link' , array(2));
                    $disable_me = TRUE;
                }
            }
        }
    }
    $disable_dropship_flage = true;
    if(get_option('cerasis_freight_quotes_store_type') == "0" && get_option('en_old_user_dropship_status') == "0")
    {
        $disable_dropship_flage = false;
    }
    $str = en_cerasis_ltl_attributes_string( $sField['attributes'] );
    $fieldsHtml .= '<p class="form-field _dropship_location">';
    $fieldsHtml .= '<label for="_dropship_location">'.$sField['label'].'</label>';
    $multi_dropship = apply_filters('cerasis_freights_quotes_plans_suscription_and_features' , 'multi_dropship');
    if($sField['type'] == 'select'){
        $fieldsHtml .= '<select name="'.$sField['name'].'" '.$str.'>';
        if ($sField['value']) {
            $count = 0;
            foreach ($sField['value'] as $option) {
                
                $disabled_option = isset($disable_dropship_flage) && ($disable_dropship_flage == true && $count > 0  && (is_array($multi_dropship)))  ? 'disabled' : '';
                
                $selected_option = en_cerasis_ltl_product_ds_selected_option($sField['selected_value'], $option['option_value']);
                $fieldsHtml .= '<option '.$disabled_option.' value="'.esc_attr($option['option_value']).'" '.$selected_option.'>'.esc_html($option['option_label']).' </option>';
            
                $count++;
            }
        }
        $fieldsHtml .= '</select>';
        $fieldsHtml .= $description;
    }
    $fieldsHtml .= '</p>';
    return $fieldsHtml;
}
    

/**
 * Drop Ship Dropdown Selected Options
 * @param $get_loc
 * @param $option_val
 * @return string
 */
function en_cerasis_ltl_product_ds_selected_option($get_loc, $option_val){
    $selected = '';
    if(is_array($get_loc)){
        if(in_array($option_val, $get_loc)){
            $selected =  'selected="selected"';
        }
    }else{
        $selected = selected($get_loc, $option_val, false);
    }
    return $selected;
}

/**
 * cerasis ltl variable fields function
 * @param $loop
 * @param $variation_data
 * @param $variation
 */
function en_cerasis_ltl_variable_fields( $loop, $variation_data, $variation ) {
    $classes = en_cerasis_ltl_freight_class_array();
    $replacement =  array( 0 => "Same as parent" );
    $options = array_replace($classes, $replacement);

    $freight_clasification = woocommerce_wp_select(
        array(
            'id'    => '_ltl_freight_variation[' . $variation->ID . ']',
            'label' => __('Freight classification <br>', 'woocommerce'),
            'value' => get_post_meta($variation->ID, '_ltl_freight_variation', true),
            'options' => $options
        )
    );
    apply_filters( 'En_Plugins_variable_freight_classification_filter', $freight_clasification );
}




add_action('woocommerce_save_product_variation', 'en_cerasis_ltl_save_variable_fields', 10, 1);
/**
 * Drop Ship Save For Variations
 * @param $post_id
 */
function en_cerasis_ltl_save_variable_fields($post_id) {
   
     if ( isset( $post_id ) && $post_id > 0 ) :
       
        $postData = $_POST;
       
        $freight_class_post = ( isset( $postData['_ltl_freight_variation'] ) ) ? sanitize_text_field($postData['action']) : "";
        
        $freight_class      = ( isset( $postData['_ltl_freight_variation'][$post_id] ) ) ?  sanitize_text_field($postData['_ltl_freight_variation'][$post_id]):"";

        $enable_ds          = ( isset( $postData['_enable_dropship'][$post_id] ) ? sanitize_text_field($postData['_enable_dropship'][$post_id]):"");
        
        $ds_locaton         = $_POST['_dropship_location'][$post_id];
        $ds_location_val    = array_map( 'intval', $ds_locaton );
        
        update_post_meta($post_id, '_enable_dropship', esc_attr($enable_ds));
        update_post_meta($post_id, '_dropship_location', maybe_serialize($ds_location_val));                
        update_post_meta($post_id, '_ltl_freight_variation', esc_attr($freight_class));
        

    endif;
}


add_action( 'woocommerce_process_product_meta', 'en_cerasis_ltl_product_fields_save' );
/**
 * Save Product Custom Shipping Options
 * @param $post_id
 */
function en_cerasis_ltl_product_fields_save( $post_id ) {
    $postData = $_POST;
    $woocommerce_checkbox   = ( isset( $postData['_enable_dropship'][$post_id] ) )   ? sanitize_text_field( $postData['_enable_dropship'][$post_id] )    : "";
    $dropship_location      = sanitize_text_field($postData['_dropship_location'][$post_id][0]);
//    $dropship_location_val  = array_map( 'intval', (int)$dropship_location );
    $dropship_location_val  = (int)$dropship_location;
    

    $shipping_freight_class = ( isset( $postData['_ltl_freight'] ) ) ? sanitize_text_field($postData['_ltl_freight']) : "";

    update_post_meta( $post_id, '_enable_dropship', esc_attr( $woocommerce_checkbox ) );
    update_post_meta( $post_id, '_dropship_location', maybe_serialize( $dropship_location_val ) );
    update_post_meta( $post_id, '_ltl_freight', esc_attr( $shipping_freight_class ) );
    
}
