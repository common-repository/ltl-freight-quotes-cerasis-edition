<?php
/**
 * Class Autoloads | loads all classes
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
* Class Autoloads | loads all classes from directories into plugin file
*/
class En_Cerasis_AutoLoader
{
    /**
     * path private variable pass the plugin folder name
     * @var private
     */
    private $path;
    
    /**
     * constructor calling
     */
    public function __construct() {
        $this->path = plugin_dir_path(__FILE__);
        spl_autoload_register(array($this, 'load'));
    }

    /**
     * function loads all files
     * @param $file
     * @return path to the main file
     */
    function load($file) {
        if (is_file($this->path . '/' . strtolower(str_replace('_', '-', $file) . '.php'))) {
            require_once( $this->path . '/' . strtolower(str_replace('_', '-', $file) . '.php') );
        }
    }
}
new En_Cerasis_AutoLoader();
new En_Cerasis_Install_Uninstall();
new En_Cerasis_Admin_Settings();
new En_Cerasis_Quotes_Request();
new En_Cerasis_Connection_Request();