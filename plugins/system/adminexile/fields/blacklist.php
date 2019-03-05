<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

require_once(JPATH_PLUGINS.'/system/adminexile/classes/aehelper.class.php');
class JFormFieldBlacklist extends JFormField
{
	protected $type = 'Blacklist';
	protected $app;
	protected $db;
	protected $formfields;
	protected function getLabel() {
            return '';
        }
	protected function getInput()
	{
	    $this->app = JFactory::getApplication();
            $this->db = JFactory::getDbo();
	    $this->formfields = $this->form->getFieldset();
            $blacklistnets = array();
            $blacklistaddresses = array();
            $blacklistinputarray=AdminExileHelper::ipArray($this->_getField('blacklist'));
            $gmp = AdminExileHelper::gmp();
            require_once(JPATH_PLUGINS.'/system/adminexile/classes/'.($gmp?'IPv6Net.class.php':'simplecidr.class.php'));
            foreach($blacklistinputarray as $blacklistitem) {
		$blacklistitem = trim($blacklistitem);
                if(preg_match('/\//',$blacklistitem)) {
                    try{
                        $blacklistnets[$blacklistitem]=$gmp?(new IPv6Net($blacklistitem)):SimpleCIDR::getInstance($blacklistitem);
                    } catch (Exception $e) {
                        error_log("AdminExile cannot process ".$blacklistitem." due to:".$e->getMessage());
                        $blacklistaddresses[trim($blacklistitem)]=$blacklistitem;
                    }
                } else {
                    $blacklistaddresses[trim($blacklistitem)]=$blacklistitem;
                }
            }
            $query = $this->db->getQuery(true);
            $query->select('*')->from('#__plg_system_adminexile')->where('penalty = 0')->order('lastattempt DESC');
            $this->db->setQuery($query);
            $blocked = $this->db->loadObjectList();
            $return=array();
            $return[]='<h3 style="float:left;clear:left;">'.($gmp?JText::_('PLG_SYS_ADMINEXILE_IPV46'):JText::_('PLG_SYS_ADMINEXILE_IPV4')).'</h3>';
            $attempts = new stdClass();
            foreach($blocked as $match) {
                if(in_array($match->ip,$blacklistaddresses)) {
                    $attempts->{$match->ip} = new stdClass();
                    $attempts->{$match->ip}->lastattempt = $match->lastattempt;
                    $attempts->{$match->ip}->firstattempt = $match->firstattempt;
                    $attempts->{$match->ip}->attempts = $match->attempts;
                } else {
                    foreach(array_keys($blacklistnets) as $key) {
                        if($blacklistnets[$key]->contains(trim((string)$match->ip))) { 
                            if(!property_exists($attempts,$key)) {
                                $attempts->$key = new stdClass();
                                $attempts->$key->attempts = 0;
                                $attempts->$key->addresses = new stdClass();
                            }                           
                            $attempts->$key->addresses->{$match->ip}->lastattempt = $match->lastattempt;
                            $attempts->$key->addresses->{$match->ip}->firstattempt = $match->firstattempt;
                            $attempts->$key->addresses->{$match->ip}->attempts = $match->attempts;
                            $attempts->$key->attempts += $match->attempts;
                        }
                    }
                }
            }
            JFactory::getDocument()->addScriptDeclaration('window.plg_sys_adminexile_blacklist = '.json_encode($attempts).';');
            return implode("\n",$return);
	}
        private function _getField($name) {
            foreach($this->formfields as $field) {
                if ( $field->name == 'jform[params]['.$name.']' || $field->name == 'jform[params]['.$name.'][]' ) {
                    return $field->value;
                }
            }               
        }
}
