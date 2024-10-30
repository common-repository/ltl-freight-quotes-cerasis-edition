<?php
/**
 * Cerasis Carrier List Class
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
       
/**
 * Cerasis Carrier List Class | all carriers display into a table in quote settings 
 */

class En_Cerasis_Carriers_Page
{
    /**
     * Carrier List Page
     */
    function __construct() {
        error_reporting(0);
    }

    /**
     * carriers table to show the carriers
     * @global $wpdb
     * @return string
     */
    function carrier_list_tab()
    {
        ?>
        <div class="carrier_section_class wrap woocommerce">
            <p>
                Identifies which carriers are included in the quote response, not what is displayed in the shopping cart. Identify what displays in the shopping cart in the Quote Settings. For example, you may include quote responses from all carriers, but elect to only show the cheapest three in the shopping cart. <br> <br> 
                Not all carriers service all origin and destination points. If a carrier doesn't service the ship to address, it is automatically omitted from the quote response. Consider conferring with Cerasis account team if you'd like to narrow the number of carrier responses. <br> <br>
            </p>
            
            
            <button class="button-primary refresh-carriers"> Refresh Carriers </button><p class="refresh-carriers refresh-carriers-loader"></p>
            
            <div style="margin: 3px 0 0 0;">
                
                <b>Automatically enable new carriers</b> &nbsp; <input id="automatically-enable" type="checkbox" <?php echo get_option('automatically_enable_new_carriers') == "yes" ? "checked='checked'" : ""; ; ?> /> &nbsp; <?php echo get_option('carriers_updated_time'); ?>
            
            </div>
            
            <table class="table table-bordered">
                <thead>
                    <tr class="even_odd_class">
                        <th>Sr#</th>
                        <th>Carrier Name</th>
                        <th>Logo</th>
                        <th>Lift gate fee</th>
                        <th><input type="checkbox" name="include_all" class="include_all" /></th>
                    </tr>
                </thead> 
                <tbody>
                <?php    
                    global $wpdb;
                    $count_carrier = 1;
                    $carriers_table = $wpdb->prefix . "carriers";
                    $cerasis_cerrier_all = $wpdb->get_results("SELECT `id`, `carrier_scac`, `carrier_name`, `carrier_logo`, `carrier_status`, `liftgate_fee` FROM ".$carriers_table." WHERE `plugin_name`='cerasis_ltl_carriers' GROUP BY `carrier_scac` ORDER BY `id` ASC");
                    
                    foreach ($cerasis_cerrier_all as $cerasis_cerrier):
                        ?>
                        <tr <?php
                        if ($count_carrier % 2 == 0) {

                            echo 'class="even_odd_class"';
                        }
                        ?> >
                            <td>
                                <?php echo $count_carrier; ?>
                            </td>

                            <td>
                                <?php echo $cerasis_cerrier->carrier_name; ?>
                            </td>
                            
                            <td>
                                <img  src="<?php echo $cerasis_cerrier->carrier_logo; ?> " >
                            </td>
                            
                            <td>
                                <input name="<?php echo $cerasis_cerrier->carrier_scac . $cerasis_cerrier->id . "liftgate_fee"; ?>" class="liftgate_fee" id="<?php echo $cerasis_cerrier->carrier_scac . $cerasis_cerrier->id . "liftgate_fee"; ?>" value="<?php echo (isset($cerasis_cerrier->liftgate_fee) && (strlen($cerasis_cerrier->liftgate_fee) > 0)) ? $cerasis_cerrier->liftgate_fee : '' ?>" type="text" >
                            </td>
                            
                            <td>
                                <input <?php
                                if ($cerasis_cerrier->carrier_status == '1') {
                                    echo 'checked="checked"';
                                }
                                ?>
                                    name="<?php echo $cerasis_cerrier->carrier_scac . $cerasis_cerrier->id; ?>" class="carrier_check" id="<?php echo $cerasis_cerrier->carrier_scac . $cerasis_cerrier->id; ?>" type="checkbox" >
                            </td>
                        </tr>
                        <?php
                        $count_carrier ++;
                    endforeach;
                    ?>                  
                    <input name="action" value="en_cerasis_save_carrier_status"  type="hidden" />
                </tbody>
            </table>
        </div>
    <?php
    }

}