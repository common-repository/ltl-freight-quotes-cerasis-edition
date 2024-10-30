<?php
    if(!function_exists("cerasis_admin_order_script"))
    {
        add_action('admin_print_scripts', 'cerasis_admin_order_script' , 101);

        function cerasis_admin_order_script() 
        {
            global $post;

?>
            <script type="text/javascript">
                
                jQuery(document).ready(function () 
                {
                    admin_order_shipping_method();

                });
                
                if(typeof admin_order_shipping_method != 'function')
                {
                    function admin_order_shipping_method()
                    {
                        jQuery( document ).ajaxComplete(function( event, xhr, settings ){
                            jQuery(".woocommerce_order_items #order_shipping_line_items .shipping_method").on('change' , function (event) {
                                
                                event.stopPropagation();
                                event.stopImmediatePropagation();
                                
                                var target = jQuery(this).val();
                                var window_fn = window[target]; 
                                if(typeof window_fn === 'function') 
                                {
                                    eval(target + "()");
                                }
                            });
                        });
                        
                    }
                }
                
                if(typeof admin_order_shipping_errors != 'function')
                {
                    function admin_order_shipping_errors(errors)
                    {
                        jQuery.each(errors, function( ind , error ) {
                            jQuery('.woocommerce_order_items').before('<div id="message" class="error inline show_order_no_quotes_msg"><p><strong>'+error+'</p></strong></div>');
                        });
                    }
                }
                
                
                function cerasis_shipping_method()
                {   
                    
                    var data = 
                    {
                        'order_id'      : <?php echo $post->ID; ?>,
                        'bill_zip'      : jQuery("#_billing_postcode").val(),
                        'ship_zip'      : jQuery("#_shipping_postcode").val(),
                        'action'        : 'en_cerasis_admin_order_quotes'
                    };
                
                    jQuery.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: data,
                        datatype: "json",
                        beforeSend: function () {
                            jQuery('.show_order_no_quotes_msg').remove();
                            jQuery('.woocommerce_order_items').before('<div class="order_waiting_bar"></div>');
                        },
                        success: function (response) {
                            jQuery('.order_waiting_bar').remove();
                            response = jQuery.parseJSON(response);
                            
                            (typeof response['errors'] != 'undefined') ? admin_order_shipping_errors(response['errors']) : "";
                            
                            if(typeof response['cost'] != "undefined" && typeof response['label'] != "undefined")
                            {
                                jQuery('.shipping_method_name').val(response['label']);
                                jQuery('input[name*="shipping_cost"]').val(response['cost']);
                                jQuery(".save-action").trigger("click");
                            }
                        },
                        error: function (request, status, error) {
                            console.log(request.responseText);
                        }
                    });
                    
                }
                
                
            </script>
            
<?php
        }
    }