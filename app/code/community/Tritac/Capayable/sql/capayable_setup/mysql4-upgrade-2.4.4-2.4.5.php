<?php
/**
 * Created by PhpStorm.
 * User: willemjan
 * Date: 13-7-15
 * Time: 16:40
 */


$this->startSetup();
$installer = $this;
$sales_setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');

$sales_setup->addAttribute('order', 'capayable_fee', 							array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee', 					    array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'capayable_fee_invoiced', 				    array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee_invoiced', 			    array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'capayable_fee_tax_amount', 				array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee_tax_amount', 			array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'capayable_fee_tax_amount_invoiced',		array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee_tax_amount_invoiced',   array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'capayable_fee_refunded', 				    array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee_refunded', 			    array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'capayable_fee_tax_amount_refunded', 		array('type' => 'decimal'));
$sales_setup->addAttribute('order', 'base_capayable_fee_tax_amount_refunded',   array('type' => 'decimal'));

$sales_setup->addAttribute('invoice', 'capayable_fee',						    array('type' => 'decimal'));
$sales_setup->addAttribute('invoice', 'base_capayable_fee',					    array('type' => 'decimal'));
$sales_setup->addAttribute('invoice', 'capayable_fee_tax_amount',				array('type' => 'decimal'));
$sales_setup->addAttribute('invoice', 'base_capayable_fee_tax_amount', 		    array('type' => 'decimal'));

$sales_setup->addAttribute('creditmemo', 'capayable_fee',						array('type' => 'decimal'));
$sales_setup->addAttribute('creditmemo', 'base_capayable_fee',				    array('type' => 'decimal'));
$sales_setup->addAttribute('creditmemo', 'capayable_fee_tax_amount',			array('type' => 'decimal'));
$sales_setup->addAttribute('creditmemo', 'base_capayable_fee_tax_amount', 	    array('type' => 'decimal'));

$sales_setup->addAttribute('quote', 'capayable_fee',							array('type' => 'decimal'));
$sales_setup->addAttribute('quote', 'base_capayable_fee',						array('type' => 'decimal'));
$sales_setup->addAttribute('quote', 'capayable_fee_tax_amount',				    array('type' => 'decimal'));
$sales_setup->addAttribute('quote', 'base_capayable_fee_tax_amount',			array('type' => 'decimal'));
$sales_setup->addAttribute('quote_address', 'capayable_fee',					array('type' => 'decimal'));
$sales_setup->addAttribute('quote_address', 'base_capayable_fee',				array('type' => 'decimal'));
$sales_setup->addAttribute('quote_address', 'capayable_fee_tax_amount',		    array('type' => 'decimal'));
$sales_setup->addAttribute('quote_address', 'base_capayable_fee_tax_amount',	array('type' => 'decimal'));

$installer->endSetup();