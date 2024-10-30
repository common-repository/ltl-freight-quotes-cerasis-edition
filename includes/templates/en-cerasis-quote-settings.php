<?php
/**
 * Cerasis Quote Settings Tab Class
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
       
/**
 * Cerasis Quote Settings Form Class
 */

class En_Cerasis_Quote_Settings
{

    /**
     * quote setting form
     * @return array
     */
    function quote_settings_tab()
    {
        $settings = array(

            'section_title_quote' => array(
                'title' => __('', 'wc-settings-cerasis_quotes'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_settings_cerasis_title_quote'
            ),

            'rating_method_cerasis' => array(
                'name' => __('Rating Method ', 'wc-settings-cerasis_quotes'),
                'type' => 'select',
                'desc' => __('Displays only the cheapest returned Rate.', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_rate_method',
                'options' => array(
                    'Cheapest' => __('Cheapest', 'Cheapest'),
                    'cheapest_options' => __('Cheapest Options', 'cheapest_options'),
                    'average_rate' => __('Average Rate', 'average_rate')
                )
            ),

            'number_of_options_cerasis' => array(
                'name' => __('Number Of Options ', 'wc-settings-cerasis_quotes'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('Number of options to display in the shopping cart.', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_Number_of_options',
                'options' => array(
                    '1' => __('1', '1'),
                    '2' => __('2', '2'),
                    '3' => __('3', '3'),
                    '4' => __('4', '4'),
                    '5' => __('5', '5'),
                    '6' => __('6', '6'),
                    '7' => __('7', '7'),
                    '8' => __('8', '8'),
                    '9' => __('9', '9'),
                    '10' => __('10', '10')
                )
            ),

            'label_as_cerasis' => array(
                'name' => __('Label As ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('What The User Sees During Checkout, e.g "Freight" Leave Blank to Display The Carrier Name.', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_label_as'
            ),

            'show_delivery_estimate_cerasis' => array(
                'name' => __('Show Delivery Estimate ', 'wc-settings-cerasis_quotes'),
                'type' => 'checkbox',
                'id' => 'wc_settings_cerasis_delivery_estimate'
            ),

            'Services_to_include_in_quoted_price_cerasis' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce-settings-cerasis_quotes'),
                'desc' => '',
                'id' => 'woocommerce_cerasis_specific_Qurt_Price',
                'css' => '',
                'default' => '',
                'type' => 'title'
            ),
            
            'residential_delivery_options_label' => array(
                'name' => __('Residential Delivery', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'residential_delivery_options_label'
            ),

            'residential_delivery_cerasis' => array(
                'name' => __('Always quote as residential delivery ', 'wc-settings-cerasis_quotes'),
                'type' => 'checkbox',
                'desc' => '',
                'id' => 'wc_settings_cerasis_residential_delivery'
            ),
            
//          Auto-detect residential addresses notification
            'avaibility_auto_residential' => array(
                'name' => __('Auto-detect residential addresses', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_auto_residential'
            ),


            'liftgate_delivery_options_label' => array(
                'name' => __('Lift Gate Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'liftgate_delivery_options_label'
            ),

            'lift_gate_delivery_cerasis' => array(
                'name'      => __('Always quote lift gate delivery ', 'wc-settings-cerasis_quotes'),
                'type'      => 'checkbox',
                'desc'      => '',
                'id'        => 'wc_settings_cerasis_lift_gate_delivery',
                'class'     => 'accessorial_service checkbox_fr_add',
            ),
            
            'cerasis_freights_liftgate_delivery_as_option' => array(
                'name'          => __('Offer lift gate delivery as an option ', 'cerasis_freights_wc_settings'),
                'type'          => 'checkbox',
                'desc'          => __('', 'cerasis_freights_wc_settings'),
                'id'            => 'cerasis_freights_liftgate_delivery_as_option',
                'class'         => 'accessorial_service checkbox_fr_add',
            ),
                        
//           Use my liftgate notification
            'avaibility_lift_gate' => array(
                'name' => __('Always include lift gate delivery when a residential address is detected', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_lift_gate'
            ),

            'hand_free_mark_up_cerasis' => array(
                'name' => __('Handling Fee / Markup ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => 'Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.',
                'id' => 'wc_settings_cerasis_hand_free_mark_up'
            ),

            'allow_for_own_arrangment_cerasis' => array(
                'name' => __('Allow For Own Arrangement ', 'wc-settings-cerasis_quotes'),
                'type' => 'checkbox',
                'desc' => __('<span class="description">Adds an option in the shipping cart for users to indicate that they will make and pay for their own LTL shipping arrangements.</span>', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_allow_for_own_arrangment'
            ),

            'text_for_own_arrangment_cerasis' => array(
                'name' => __('Text For Own Arrangement ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => '',
                'default' => "I'll arrange my own freight",
                'id' => 'wc_settings_cerasis_text_for_own_arrangment'
            ),

            'allow_other_plugins' => array(
                'name' => __('Show WooCommerce Shipping Options ', 'wc-settings-cerasis_quotes'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('Enabled options on WooCommerce Shipping page are included in quote results.', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_allow_other_plugins',
                'options' => array(
                    'yes' => __('YES', 'YES'),
                    'no' => __('NO', 'NO'),
                )
            ),

            'return_LTL_quotes_cerasis' => array(
                'name' => __('Return Cerasis Freight quotes when an order parcel shipment weight exceeds 150 lbs ', 'wc-settings-cerasis_quotes'),
                'type' => 'checkbox',
                'desc' => '<span class="description" >When checked, the LTL Freight Quote plugin will return quotes when an order’s total weight exceeds 150 lbs (the maximum permitted by FedEx and UPS), even if none of the products have settings to indicate that it will ship LTL Freight. To increase the accuracy of the returned quote(s), all products should have accurate weights and dimensions. </span>',
                'id' => 'en_plugins_return_LTL_quotes'
            ),

            'section_end_quote' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_quote_section_end'
            )
        );
        return $settings;
    }

}