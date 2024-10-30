<?php

if(!defined("ABSPATH"))
{
    exit();
}

if(!class_exists("En_Cerasis_Liftgate_As_Option"))
{
    class En_Cerasis_Liftgate_As_Option{
        
        /**
         * set flag
         * @var string type 
         */
        public $cerasis_freights_as_option;
        
        /**
         * label sufex
         * @var array type 
         */
        public $label_sfx_arr;
        
        public function __construct()
        {
            $this->cerasis_freights_as_option = get_option("cerasis_freights_liftgate_delivery_as_option");
            $this->label_sfx_arr = array();
        }
        
        /**
         * Update carrier request
         * @param array type $post_data
         * @return array type
         */
        public function cerasis_freights_update_carrier_service( $post_data )
        {
            if(isset($this->cerasis_freights_as_option) && ($this->cerasis_freights_as_option == "yes"))
            {
                $post_data['liftGateAsAnOption'] = '1';
            }
            
            return $post_data;
        }
        
        /**
         * Get all code and value and set in array
         * @param array type $surcharges
         * @return array type
         */
        public function update_parse_cerasis_freights_output( $surcharges )
        {   
            $surcharge_amount = array();      
            foreach ($surcharges as $key => $surcharge) {
                $type_code = $surcharge->Type->Code;
                $factor_value = $surcharge->Factor->Value;
                if(isset($type_code) && (isset($factor_value))){
                    $surcharge_amount[$type_code] = $factor_value;
                }
            }

            return $surcharge_amount;
        }
        
        /**
         * If lift gate as option checked duplicate array created
         * @param array type $rate
         * @return array
         */
        public function update_rate_whn_as_option_cerasis_freights( $rate )
        {
            if(isset($rate) && (!empty($rate))){
                $rate = apply_filters("en_woo_addons_web_quotes" , $rate , en_woo_plugin_cerasis_freights);
                
                $label_sufex = (isset($rate['label_sufex'])) ? $rate['label_sufex'] : array();  
                $label_sufex = $this->label_R_cerasis_freights($label_sufex);
                $rate['label_sufex'] = $label_sufex;
                
                if(isset($this->cerasis_freights_as_option,$rate['grandTotalWdoutLiftGate']) && 
                        ($this->cerasis_freights_as_option == "yes") && ($rate['grandTotalWdoutLiftGate'] > 0) )
                { 
                    $lift_resid_flag = get_option( 'en_woo_addons_liftgate_with_auto_residential' );

                    if(isset($lift_resid_flag) && 
                            ( $lift_resid_flag == "yes" ) && 
                            (in_array("R", $label_sufex)))
                    {
                        return $rate;
                    }
                    
                    (isset($rate['label_sufex'])) ? $rate['label_sufex'] = array() : "";  
                    
                    $wdout_lft_gte = $rate;
                    $rate['append_label'] = " with lift gate delivery ";
                    $wdout_lft_gte['cost'] = $wdout_lft_gte['grandTotalWdoutLiftGate'];
                    $wdout_lft_gte['id'] .= "_wdout_lft_gte";
                    $rate = array($rate , $wdout_lft_gte);
                }
            }
            
            return $rate;
        }
        
        /**
         * Get label array from response 
         * @param array type $result
         * @return array type
         */
        public function filter_label_sufex_array_cerasis_freights($result)
        {
            $this->check_residential_status( $result );
            (isset($result['autoResidentialsStatus']) && ($result['autoResidentialsStatus'] == "r")) ? array_push($this->label_sfx_arr, "R") : "";
            (isset($result['liftGateStatus']) && ($result['liftGateStatus'] == "l")) ? array_push($this->label_sfx_arr, "L") : "";
            return array_unique($this->label_sfx_arr);
        }

        /**
         * check and update residential tatus
         * @param array type $result
         */
        public function check_residential_status( $result )
        {
            $residential_detecion_flag          = get_option("en_woo_addons_auto_residential_detecion_flag");
            $auto_renew_plan                    = get_option("auto_residential_delivery_plan_auto_renew");

            if(($auto_renew_plan == "disable") && 
                    ($residential_detecion_flag == "yes") && 
                    (isset($result['autoResidentialSubscriptionExpired'])) &&
                    ( $result['autoResidentialSubscriptionExpired'] == 1 ))
            {
                update_option("en_woo_addons_auto_residential_detecion_flag" , "no");
            }
        }
        
        /**
         * check "R" in array
         * @param array type $label_sufex
         * @return array type
         */
        public function label_R_cerasis_freights($label_sufex)
        {
            if(get_option('ups_freight_setting_residential') == 'yes' && (in_array("R", $label_sufex)))
            {
                $label_sufex = array_flip($label_sufex);
                unset($label_sufex['R']);
                $label_sufex = array_keys($label_sufex);

            }
            
            return $label_sufex;
        }
    }
    
    new En_Cerasis_Liftgate_As_Option();
}

