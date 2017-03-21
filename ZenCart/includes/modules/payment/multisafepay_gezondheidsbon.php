<?php

require( "multisafepay.php" );

class multisafepay_gezondheidsbon extends multisafepay {

    var $icon = "gezondheidsbon.png";

    /*
     * Constructor
     */

    function __construct()
    {
        global $order;
        $this->code = 'multisafepay_gezondheidsbon';
        $this->title = $this->getTitle(MODULE_PAYMENT_MSP_GEZONDHEIDSBON_TEXT_TITLE);
        $this->public_title = $this->getTitle(MODULE_PAYMENT_MSP_GEZONDHEIDSBON_TEXT_TITLE);
        $this->description = $this->description = "<img src='images/icon_info.gif' border='0'>&nbsp;<b>MultiSafepay GezondheidsBon</b><BR>The main MultiSafepay module must be installed (does not have to be active) to use this payment method.<BR>";
        $this->enabled = MODULE_PAYMENT_MSP_GEZONDHEIDSBON_STATUS == 'True';
        $this->sort_order = MODULE_PAYMENT_MSP_GEZONDHEIDSBON_SORT_ORDER;


        if (is_object($order)) {
            $this->update_status();
        }
    }

    /*
     * Check whether this payment module is available
     */

    function update_status()
    {
        global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_MSP_GEZONDHEIDSBON_ZONE > 0)) {
            $check_flag = false;
            $check_query = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_MSP_GEZONDHEIDSBON_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while (!$check_query->EOF) {
                if ($check_query->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check_query->fields['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $check_query->MoveNext();
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function process_button()
    {

        return zen_draw_hidden_field('msp_paymentmethod', 'GEZONDHEIDSBON');
    }

    /*
     * Checks whether the payment has been “installed” through the admin panel
     */

    function check()
    {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    /*
     * Installs the configuration keys into the database
     */

    function install()
    {
        global $db;

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable MultiSafepay GezondheidsBon Module', 'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_STATUS', 'True', 'Do you want to accept GezondheidsBon payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '3', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    }

    /**
     * 
     * @return type
     */
    function keys()
    {
        return array
            (
            'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_STATUS',
            'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_SORT_ORDER',
            'MODULE_PAYMENT_MSP_GEZONDHEIDSBON_ZONE',
        );
    }

}

?>