<?php
/**
 * WC Cerasis Connection Settings Tab Class
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
       
/**
 * Cerasis Test Connection Settings Form Class
 */
class En_Cerasis_Test_Connection_Page
{

    /**
     * test connection setting form
     * @return array
     */
    function connection_setting_tab()
    {

        $settings = array(
            'section_title_wc_cerasis' => array(
                'name' => __('', 'wc-settings-cerasis_quotes'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'wc_settings_cerasis_title_section_connection'
            ),

            'wc_cerasis_shipper_id' => array(
                'name' => __('Shipper ID ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_shipper_id',
                'placeholder' => 'Shipper ID'
            ),

            'wc_cerasis_username' => array(
                'name' => __('Username ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_username',
                'placeholder' => 'Username'
            ),

            'wc_cerasis_password' => array(
                'name' => __('Password ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_password',
                'placeholder' => 'Password'
            ),

            'wc_cerasis_authentication_key' => array(
                'name' => __('Authentication Key ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_authentication_key',
                'placeholder' => 'Authentication Key'
            ),

            'wc_cerasis_plugin_licence_key' => array(
                'name' => __('Plugin License Key ', 'wc-settings-cerasis_quotes'),
                'type' => 'text',
                'desc' => __('Obtain a License Key from <a href="https://eniture.com/products/" target="_blank" >eniture.com </a>', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_licence_key',
                'placeholder' => 'Plugin License Key'
            ),

            'wc_cerasis_save_buuton' => array(
                'name' => __('Save Button ', 'wc-settings-cerasis_quotes'),
                'type' => 'button',
                'desc' => __('', 'wc-settings-cerasis_quotes'),
                'id' => 'wc_settings_cerasis_button'
            ),

            'wc_cerasis_section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_cerasis_end-section_connection'
            ),
        );
        return $settings;
    }

}

