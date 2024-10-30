<?php
/**
 * Plugin Settings Tabs | plugin settings tabs into wooCommerce settings
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
/**
 * Plugin Settings Tabs | plugin settings tabs into wooCommerce settings
 */
class En_Cerasis_Settings_Tabs_Class extends WC_Settings_Page
{
    /**
     * settings tabs class constructor
     */
    public function __construct()
    {
        $this->id = 'cerasis_freights';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * adding tabs name to existing tabs in wooCommerce settings
     * @param $settings_tabs
     * @return array
     */
    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs[$this->id] = __('Cerasis Freight', 'woocommerce-settings-cerasis_quotes');
        return $settings_tabs;
    }

    /**
     * Settings sections names
     * @return array
     */
    public function get_sections()
    {   
        $sections = array(
            ''          => __('Connection Settings', 'woocommerce-settings-cerasis_quotes'),
            'section-1' => __('Carriers', 'woocommerce-settings-cerasis_quotes'),
            'section-2' => __('Quote Settings', 'woocommerce-settings-cerasis_quotes'),
            'section-3' => __('Warehouses', 'woocommerce-settings-cerasis_quotes'),
            'section-4' => __('User Guide', 'woocommerce-settings-cerasis_quotes'),
        );
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * section page for warehouse and dropship settings
     */
    public function warehouse_dropship_page()
    {
        require_once plugin_dir_path(__FILE__).'warehouse-dropship/wild/warehouse/warehouse_template.php';
        require_once plugin_dir_path(__FILE__).'warehouse-dropship/wild/dropship/dropship_template.php';
    }

    /**
     * section page for user guide 
     */
    public function user_guide_page()
    {
        include_once plugin_dir_path(__FILE__).'templates/en-cerasis-user-guide.php';
    }

    /**
     * get settings for all sections
     * @param $section
     * @return string
     */
    public function get_settings($section = null)
    {   
        include_once plugin_dir_path(__FILE__).'templates/en-cerasis-test-connection.php';
        include_once plugin_dir_path(__FILE__).'templates/en-cerasis-quote-settings.php';
        include_once plugin_dir_path(__FILE__).'templates/en-cerasis-carriers-page.php';
        $conn_set_obj = new En_Cerasis_Test_Connection_Page();
        $quote_set_obj = new En_Cerasis_Quote_Settings();
        $cerrier_obj = new En_Cerasis_Carriers_Page();
        switch ($section) {

            case 'section-0' :
                echo '<div class="cerasis_connection_section_class">';
                $settings = $conn_set_obj->connection_setting_tab();
                break;

            case 'section-1':
                echo '<div class="carrier_section_class">';
                $settings = $cerrier_obj->carrier_list_tab();
                break;

            case 'section-2':
                echo '<div class="quote_section_class_ltl">';
                $settings = $quote_set_obj->quote_settings_tab();
                break;

            case 'section-3' :
                $this->warehouse_dropship_page();
                $settings = array();
                break;

            case 'section-4' :
                $this->user_guide_page();
                $settings = array();
                break;

            default:
                echo '<div class="cerasis_connection_section_class">';
                $settings = $conn_set_obj->connection_setting_tab();
                break;
        }
        
        $settings = apply_filters('en_woo_addons_settings', $settings, $section, en_woo_plugin_cerasis_freights);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('wc-settings-cerasis_quotes', $settings, $section);
    }
    
    /**
    * avaibility_addon 
    * @param array type $settings
    * @return array type
    */
    function avaibility_addon($settings)
    {
        if(is_plugin_active('residential-address-detection/residential-address-detection.php')) 
        {     
            unset($settings['avaibility_lift_gate']);   
            unset($settings['avaibility_auto_residential']);   
        }

        return $settings;
    }

    /**
     * output function calling
     * @global current_section
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * saving all settings to wooCommerce settings
     * @global $current_section
     */
    public function save() {
        global $current_section;
        if ($current_section != 'section-1') {
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::save_fields($settings);
        }
    }

}
new En_Cerasis_Settings_Tabs_Class();