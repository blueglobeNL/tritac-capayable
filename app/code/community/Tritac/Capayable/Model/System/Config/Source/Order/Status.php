<?php
class Tritac_Capayable_Model_System_Config_Source_Order_Status extends Mage_Adminhtml_Model_System_Config_Source_Order_Status {
	
	public function toOptionArray(){
	
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => Mage::helper('capayable')->__('-- Please Select --')
            );
        foreach ($statuses as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;		
		
	}
	
	public function orderStatusPaid(){
		$default = Mage::helper('capayable')->getOrderStatusPaid();
		$statuses = Mage::getSingleton('sales/order_config')->getStatuses();
		$default_label = (isset($statuses[$default]))?$statuses[$default]:$default;
		$options = array('' => Mage::helper('capayable')->__('-- Use Default -- ( %s )',$default_label));
        foreach ($statuses as $code=>$label) {
            $options[$code] = $label;
		}
        return $options;
	}
	
	
	public function orderStatusNew(){
		$default = Mage::helper('capayable')->getNewOrderStatus();
		$statuses = Mage::getSingleton('sales/order_config')->getStatuses();
		$default_label = (isset($statuses[$default]))?$statuses[$default]:$default;
		$options = array('' => Mage::helper('capayable')->__('-- Use Default -- ( %s )',$default_label));
        foreach ($statuses as $code=>$label) {
            $options[$code] = $label;
		}
        return $options;
	}

}