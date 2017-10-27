<?php
/**
 * @category    Tritac
 * @package     Tritac_Capayable
 * @copyright   Copyright (c) 2016 BlueGlobe/Tritac
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      Isolde van Oosterhout
 */
class Tritac_Capayable_OrderController extends Mage_Core_Controller_Front_Action {

    public function returnAction() {
        $this->_redirect('checkout/onepage/success');
    }
}