<?php
/**
 * @copyright   Copyright (C) 2005 - 2013 MIchael Richey. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Core Platform.
 * Adjustment to the textarea field which re-implements the translate_default attribute
 */
class JFormFieldAETextarea extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'AETextarea';
        
	public function setup(SimpleXMLElement $element, $value, $group = null) {
            $return = parent::setup($element, $value, $group);
            if($return) {
                if(version_compare(JVERSION,3.2,'>=')) {
                    $td = ($this->getAttribute('translate_default') == 'true')?true:false;
                    $default = ($this->value == $this->default)?$this->default:false;
                } else {
                    $td = ($element->attributes()->translate_default == 'true')?true:false;
                    $default = ($this->value == $element->attributes()->default)?$element->attributes()->default:false;                
                }
                if($td && $default) {
                    $this->value = JText::_($default);   
                }
            }
            return $return;
        }
        
        public function getInput() {
            $input=array(
                '<table class="'.$this->id.' table table-striped"></table>',
                '<button class="'.$this->id.'">'.JText::_('PLG_SYS_ADMINEXILE_BUTTON_ADD_IP').'</button>',
                '<input type="hidden" id="'.$this->id.'" name="'.$this->name.'" class="'.$this->element['class'].'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"\>'
            );
            return implode("\n",$input);
        }
        
}
