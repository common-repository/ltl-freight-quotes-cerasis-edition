<?php
/**
 * Warehouse Dropship Ajax Request | getting address from zip code ajax call
 * @package     Woocommerce Cerasis Edition
 * @author      <https://eniture.com/>
 * @version     v.1..0 (01/10/2017)
 * @copyright   Copyright (c) 2017, Eniture
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Warehouse Dropship Ajax Request | getting address from zip code ajax call
 */
class Eh_Ds_Ajax_Class
{
    /**
     * Warehouse Dropship Ajax Request Constructor
     */
    function __construct() {   
        add_action('admin_footer', array($this, 'dropship_ajax_script'));        
        add_action('admin_footer', array($this, 'warehouse_ajax_script'));
    }
     /**
     * dropship ajax call for auto fill form according to zip code
     * @return string/array
     */
    function dropship_ajax_script(){
        ?>
        <script>
            jQuery(document).ready(function () {
            jQuery('.hide_drop_val').click(function () {
                jQuery('#edit_dropship_form_id').val('');
                jQuery("#ltl_dropship_zip").val('');
                jQuery('.city_select').hide();
                jQuery('.city_input').show();
                jQuery('#ltl_dropship_city').css('background', 'none');
                jQuery("#ltl_dropship_nickname").val('');
                jQuery("#ltl_dropship_city").val('');
                jQuery('.ltl_multi_state').empty();
                jQuery("#ltl_dropship_state").val('');
                jQuery("#ltl_dropship_country").val('');
                jQuery('.ltl_zip_validation_err').hide();
                jQuery('.ltl_city_validation_err').hide();
                jQuery('.ltl_state_validation_err').hide();
                jQuery('.ltl_country_validation_err').hide();
                jQuery( '.not_allowed' ).hide();
                jQuery( '.already_exist' ).hide();
                jQuery( '.wrng_credential' ).hide();
            });

            jQuery('.ltl_add_dropship_btn').click(function () {

                setTimeout(function () {

                    if (jQuery('.ds_popup').is(':visible')) {
                        jQuery('.ds_input > input').eq(0).focus();
                    }
                }, 500);
            });

            jQuery("#ltl_dropship_zip").keypress(function (e) {

                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
                }
            });

            jQuery("#ltl_dropship_zip").on('change', function () {

                if (jQuery("#ltl_dropship_zip").val() == '') {
                    return false;
                }
                jQuery('#ltl_dropship_city').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ltl_dropship_state').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('.city_select_css').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ltl_dropship_country').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');

                var postForm = {
                    'action': 'get_address',
                    'origin_zip': jQuery('#ltl_dropship_zip').val(),
                };

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: postForm,
                    dataType: 'json',
                    beforeSend: function () 
                    {
                        jQuery('.ltl_zip_validation_err').hide();
                        jQuery('.ltl_city_validation_err').hide();
                        jQuery('.ltl_state_validation_err').hide();
                        jQuery('.ltl_country_validation_err').hide();
                    },
                    success: function (data) {

                        if (data) {

                            if (data.country === 'US' || data.country === 'CA') {

                                if (data.postcode_localities == 1) {

                                    jQuery('.city_select').show();
                                    jQuery('#dropship_actname').replaceWith(data.city_option);
                                    jQuery('#ltl_dropship_state').val(data.state);
                                    jQuery('#ltl_dropship_country').val(data.country);
                                    jQuery('.city-multiselect').change(function () {
                                        setLtlDsCity(this);
                                    });
                                    jQuery('#ltl_dropship_city').val(data.first_city);
                                    jQuery('#ltl_dropship_state').css('background', 'none');
                                    jQuery('.city_select_css').css('background', 'none');
                                    jQuery('#ltl_dropship_country').css('background', 'none');
                                    jQuery('.city_input').hide();

                                } else {

                                    jQuery('.city_input').show();
                                    jQuery('#_city').removeAttr('value');
                                    jQuery('.city_select').hide();
                                    jQuery('#ltl_dropship_city').val(data.city);
                                    jQuery('#ltl_dropship_state').val(data.state);
                                    jQuery('#ltl_dropship_country').val(data.country);
                                    jQuery('#ltl_dropship_city').css('background', 'none');
                                    jQuery('#ltl_dropship_state').css('background', 'none');
                                    jQuery('#ltl_dropship_country').css('background', 'none');
                                }
                            } else if (data.result === 'false') {

                                jQuery('#ltl_dropship_city').css('background', 'none');
                                jQuery('#ltl_dropship_state').css('background', 'none');
                                jQuery('#ltl_dropship_country').css('background', 'none');

                            }else if( data.apiResp === 'apiErr' ){
                                jQuery( '.wrng_credential' ).show('slow');
                                jQuery( '#ltl_dropship_city' ).css('background', 'none');
                                jQuery( '#ltl_dropship_state' ).css('background', 'none');
                                jQuery( '#ltl_dropship_country' ).css('background', 'none');    
                                setTimeout(function () {
                                    jQuery('.wrng_credential').hide('slow');
                                }, 5000);
                            }  else {

                                jQuery('.not_allowed').show('slow');
                                jQuery('#ltl_dropship_city').css('background', 'none');
                                jQuery('#ltl_dropship_state').css('background', 'none');
                                jQuery('#ltl_dropship_country').css('background', 'none');
                                setTimeout(function () {
                                    jQuery('.not_allowed').hide('slow');
                                }, 5000);
                            }
                        }
                    },
                });
                return false;
            });
        });

        function setLtlDsCity($this) {

            var city = jQuery($this).val();
            jQuery('#ltl_dropship_city').val(city);
        }

        jQuery(function () {

            jQuery('input.alphaonly').keyup(function () {

                if (this.value.match(/[^a-zA-Z ]/g)) {
                    this.value = this.value.replace(/[^a-zA-Z ]/g, '');
                }
            });
        });
    </script>
        <?php
    }
    
    /**
     * warehouse ajax call for auto fill form according to zip code
     * @return  string/array
     */
    function warehouse_ajax_script(){
        ?>
        <script type="text/javascript">

            jQuery(document).ready(function () {
                window.location.href = jQuery('.close').attr('href');
                jQuery('.hide_val').click(function () {

                    jQuery('#edit_form_id').val('');
                    jQuery("#ltl_origin_zip").val('');
                    jQuery('.city_select').hide();
                    jQuery('.city_input').show();
                    jQuery('#ltl_origin_city').css('background', 'none');
                    jQuery("#ltl_origin_city").val('');
                    jQuery("#ltl_origin_state").val('');
                    jQuery("#ltl_origin_country").val('');
                    jQuery('.ltl_zip_validation_err').hide();
                    jQuery('.ltl_city_validation_err').hide();
                    jQuery('.ltl_state_validation_err').hide();
                    jQuery('.ltl_country_validation_err').hide();
                    jQuery( '.not_allowed' ).hide();
                    jQuery( '.wrng_credential' ).hide();
                });

                jQuery('.ltl_add_warehouse_btn').click(function () {

                    setTimeout(function () {

                        if (jQuery('.ltl_add_warehouse_popup').is(':visible')) {
                            jQuery('.ltl_add_warehouse_input > input').eq(0).focus();
                        }
                    }, 500);
                });

                jQuery("#ltl_origin_zip").keypress(function (e) {

                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {

                        return false;
                    }
                });

                jQuery("#ltl_origin_zip").on('change', function () {

                    if (jQuery("#ltl_origin_zip").val() == '') {

                        return false;
                    }

                    jQuery('#ltl_origin_city').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#ltl_origin_state').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('.city_select_css').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');
                    jQuery('#ltl_origin_country').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/ltl-freight-quotes-cerasis-edition/assets/icons/processing.gif") no-repeat scroll 50% 50%');

                    var postForm = {
                        'action': 'get_address',
                        'origin_zip': jQuery('#ltl_origin_zip').val(),
                    };

                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: postForm,
                        dataType: 'json',
                        beforeSend: function () 
                        {
                            jQuery('.ltl_zip_validation_err').hide();
                            jQuery('.ltl_city_validation_err').hide();
                            jQuery('.ltl_state_validation_err').hide();
                            jQuery('.ltl_country_validation_err').hide();
                        },
                        success: function (data) {

                            if (data) {

                                if (data.country === 'US' || data.country === 'CA') {
                                    if (data.postcode_localities == 1) {
                                        jQuery('.city_select').show();
                                        jQuery('#actname').replaceWith(data.city_option);
                                        jQuery('.ltl_multi_state').replaceWith(data.city_option);
                                        jQuery('.city-multiselect').change(function () {
                                            setLTLCity(this);
                                        });
                                        jQuery('#ltl_origin_city').val(data.first_city);
                                        jQuery('#ltl_origin_state').val(data.state);
                                        jQuery('#ltl_origin_country').val(data.country);
                                        jQuery('#ltl_origin_state').css('background', 'none');
                                        jQuery('.city_select_css').css('background', 'none');
                                        jQuery('#ltl_origin_country').css('background', 'none');
                                        jQuery('.city_input').hide();
                                    } else {
                                        jQuery('.city_input').show();
                                        jQuery('#_city').removeAttr('value');
                                        jQuery('.city_select').hide();
                                        jQuery('#ltl_origin_city').val(data.city);
                                        jQuery('#ltl_origin_state').val(data.state);
                                        jQuery('#ltl_origin_country').val(data.country);
                                        jQuery('#ltl_origin_city').css('background', 'none');
                                        jQuery('#ltl_origin_state').css('background', 'none');
                                        jQuery('#ltl_origin_country').css('background', 'none');
                                    }
                                } else if (data.result === 'false') {
                                    jQuery('#ltl_origin_city').css('background', 'none');
                                    jQuery('#ltl_origin_state').css('background', 'none');
                                    jQuery('#ltl_origin_country').css('background', 'none');
                                }else if( data.apiResp === 'apiErr' ){
                                    jQuery( '.wrng_credential' ).show('slow');
                                    jQuery( '#ltl_origin_city' ).css('background', 'none');
                                    jQuery( '#ltl_origin_state' ).css('background', 'none');
                                    jQuery( '#ltl_origin_country' ).css('background', 'none');    
                                    setTimeout(function () {
                                        jQuery('.wrng_credential').hide('slow');
                                    }, 5000);
                                } else {
                                    jQuery('.not_allowed').show('slow');
                                    jQuery('#ltl_origin_city').css('background', 'none');
                                    jQuery('#ltl_origin_state').css('background', 'none');
                                    jQuery('#ltl_origin_country').css('background', 'none');
                                    setTimeout(function () {
                                        jQuery('.not_allowed').hide('slow');
                                    }, 5000);
                                }
                            }
                        },
                    });
                    return false;
                });
            });
            function setLTLCity($this) {

                var city = jQuery($this).val();
                jQuery('#ltl_origin_city').val(city);
            }
            jQuery(function () {

                jQuery('input.alphaonly').keyup(function () {

                    if (this.value.match(/[^a-zA-Z ]/g)) {

                        this.value = this.value.replace(/[^a-zA-Z ]/g, '');
                    }
                });
            });
        </script>
        <?php
    }
}
