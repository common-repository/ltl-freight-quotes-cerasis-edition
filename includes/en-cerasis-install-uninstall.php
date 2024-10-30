<?php
/**
 * LTL Freight Plugin Installation |  Cerasis Edition
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * LTL Freight Plugin Installation |  Cerasis Edition
 */
class En_Cerasis_Install_Uninstall
{

    /**
    * constructor
    */
    public function __constructor() {

    }

    /**
     * Plugin installation script
     */
     public static function install() {
        global $wpdb;
        add_option('wc_cerasis_edition', '1.0.0', '', 'yes');
        add_option('wc_cerasis_db_version', '1.0.0');
        $eniture_plugins = get_option('EN_Plugins');
        if (!$eniture_plugins) {
            add_option('EN_Plugins', json_encode(array('cerasis_shipping_method')));
        } else {
            $plugins_array = json_decode($eniture_plugins);
            if (!in_array('cerasis_shipping_method', $plugins_array)) {
                array_push($plugins_array, 'cerasis_shipping_method');
                update_option('EN_Plugins', json_encode($plugins_array));
            }
        }
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        //carriers table
        $carriers_table = $wpdb->prefix . "carriers";
        $sql = "CREATE TABLE IF NOT EXISTS $carriers_table (
        id int(10) NOT NULL AUTO_INCREMENT,
        carrier_scac varchar(600) NOT NULL,
        carrier_name varchar(600) NOT NULL,
        carrier_logo varchar(255) NOT NULL,
        carrier_status varchar(8) NOT NULL,
        plugin_name varchar(100) NOT NULL,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        dbDelta($sql);
        
        //Alter Table
        $myLiftgate = $wpdb->get_row( sprintf("SELECT `liftgate_fee` FROM %s LIMIT 1", $carriers_table) );
        if(!isset($myLiftgate->liftgate_fee)) 
        {
            $wpdb->query( sprintf( "ALTER TABLE %s ADD COLUMN liftgate_fee VARCHAR(255) NOT NULL", $carriers_table) );
        }
        
       //warehouse table
       $warehouse_table = $wpdb->prefix . "warehouse";
        $origin = 'CREATE TABLE IF NOT EXISTS ' . $warehouse_table . '(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city varchar(200) NOT NULL,
            state varchar(200) NOT NULL,
            zip varchar(200) NOT NULL,
            country varchar(200) NOT NULL,
            location varchar(200) NOT NULL,
            nickname varchar(200) NOT NULL,            
            PRIMARY KEY  (id) )';
            dbDelta($origin);

            $myCustomer = $wpdb->get_row( sprintf("SELECT `enable_store_pickup` FROM %s LIMIT 1", $warehouse_table) );

            if(!isset($myCustomer->enable_store_pickup)) 
            {

                $wpdb->query( sprintf( "ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                                                    . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table) );

            }
        $En_Cerasis_Carrier_List = new En_Cerasis_Carrier_List();
        $En_Cerasis_Carrier_List->carriers();
    }

    /**
     * Plugin un-installation script
     */
    public static function uninstall() {
        //code for uninstallation
        global $wpdb;
        $option_name = 'wc_cerasis_edition';
        delete_option($option_name);
        // for site options in Multisite
        delete_site_option($option_name);
        // delete carriers of this plugin
        $sql = $wpdb->prepare(
            "DELETE FROM %s WHERE plugin_name LIKE 'cerasis_ltl_carriers';",
             $wpdb->prefix . "carriers"
        );
        $wpdb->query($sql);
        // Delete options.
        $sql = $wpdb->prepare(
            "DELETE FROM %s WHERE option_name LIKE 'wc_cerasis\_%';",
             $wpdb->prefix . "options"
        );
        $wpdb->query($sql);
        // Clear any cached data that has been removed
        wp_cache_flush();
    }
}