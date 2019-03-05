<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
JFormHelper::loadFieldClass('list');
 
class JFormFieldParentDonation extends JFormFieldList {
 
	protected $type = 'ParentDonation';
 
	public function getOptions() {
                $app = JFactory::getApplication();
                $parentdonation = $app->input->get('parentdonation'); 
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('a.name,a.id')->from('`#__team_donation_types` AS a')->where('a.parent_id = 0 ');
				$rows = $db->setQuery($query)->loadObjectlist();
				$i=0;
				$options[$i]['value']="0";
				$options[$i]['text']="No parent";	
				$i++;
				
                foreach($rows as $row){
					$options[$i]['value'] = $row->id;
                    $options[$i]['text'] = $row->name;
					$i++;
                }
                // Merge any additional options in the XML definition.
				//$options = array_merge(parent::getOptions(), $parents);
                return $options;
	}
}