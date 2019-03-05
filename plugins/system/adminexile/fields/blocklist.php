<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldBlocklist extends JFormField
{
	protected $type = 'Blocklist';
	protected $app;
	protected function getLabel() {
            return '';
        }
	protected function getInput()
	{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('TIME_TO_SEC(TIMEDIFF(ADDDATE(`lastattempt`,INTERVAL `penalty` MINUTE),NOW())) AS timeleft,ip,firstattempt,lastattempt,attempts,penalty')->from('#__plg_system_adminexile')->where('penalty > 0')->order('lastattempt DESC');
            $db->setQuery($query);
            $blocked = $db->loadObjectList();
            if(!count($blocked)) return '';
            $version = new JVersion;
            $return=array();
            $deletetext = '';
            if(version_compare(JVERSION,'3.2','>=')) {
                $deletetext = JText::_('JACTION_DELETE');
            }
            $return[]='<table class="table table-condensed table-striped bruteforce"><tr><th>IP</th><th>'.JText::_('PLG_SYS_ADMINEXILE_FIRST_ATTEMPT').'</th><th>'.JText::_('PLG_SYS_ADMINEXILE_LAST_ATTEMPT').'</th><th>'.JText::_('PLG_SYS_ADMINEXILE_ATTEMPTS').'</th><th>'.JText::_('PLG_SYS_ADMINEXILE_PENALTY').'</th><td></td></tr>';
            foreach($blocked as $match) {
                if($match->timeleft <= 0) {
                    $query = $db->getQuery(true);
                    $query->delete('#__plg_system_adminexile')->where('ip = '.$db->quote($match->ip));
                    $db->setQuery($query);
                    $db->query();                    
                } else {
                    $buttons = '<button class="btn btn-mini removeblock hasTip" data-block="'.htmlentities(json_encode(array('ip'=>$match->ip,'firstattempt'=>$match->firstattempt))).'" data-toggle="tooltip" title="'.JText::_('JACTION_DELETE').'"><i class="icon-trash"></i>'.$deletetext.'</button>';
                    $return[]='<td>'.$match->ip.'</td><td>'.$match->firstattempt.'</td><td>'.$match->lastattempt.'</td><td>'.$match->attempts.'</td><td>'.$match->penalty.'</td><td>'.$buttons.'</td></tr>';
                }
            }
            $return[]='</table>';
            return implode("\n",$return);
	}
}
