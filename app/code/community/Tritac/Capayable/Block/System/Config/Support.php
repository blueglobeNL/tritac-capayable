<?php
/**
 * Jaagers B.v.
 * @category   Mempay
 * @package    Jaagers_Mempay
 * @author     Ricardo van der Vaart
 * @copyright  Copyright (c) 2015 Jaagers B.v. (http://www.jaagers.com)
 * @license    Jaagers B.v.
 */

class Tritac_Capayable_Block_System_Config_Support extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Set template
     */
    public function __construct()
    {
        parent::_prepareLayout();
        $this->setTemplate('capayable/support.phtml');
    }

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

}