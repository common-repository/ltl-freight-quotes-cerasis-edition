<?php
/**
 * User Guide Page | user guide page to show instructions to user how to use plugin
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
$urlEnit = "https://eniture.com/woocommerce-cerasis-ltl-freight/#documentation";
?>

<div class="user_guide">
    <p>
        The User Guide for this application is maintained on the publisher's website. To view it click 
        <a href="<?php echo esc_url($urlEnit); ?>" target="_blank">
            here
        </a> 
        or paste the following link into your browser.
    </p>
<?php echo esc_url($urlEnit); ?>