<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
//define('JPATH_BASE', dirname(__FILE__) );
JLoader::register('BooksMapsHelperThumbs', JPATH_ADMINISTRATOR . '/components/com_booksmaps/helpers/thumbnails.php');

use Joomla\Registry\Registry;

//JLoader::register('ContactHelper', JPATH_ADMINISTRATOR . '/components/com_teams/helpers/contact.php');

/**
 * Item Model for a Contact.
 *
 * @since  1.6
 */
class TeamsModelTeam extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_teams.team';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}

			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_teams.team.' . (int) $record->id);
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		// Check against the category.
		if (!empty($record->id))
		{
			$user = JFactory::getUser();

			return $user->authorise('core.edit.state', 'com_teams.team.' . (int) $record->id);
		}
		// Default to component settings if category not known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Team', $prefix = 'TeamsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_users/models/fields');

		// Get the form.
		$form = $this->loadForm('com_teams.team', 'team', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
		/*	// Convert the metadata field to an array.
			$registry = new Registry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();*/
		}

		// Load associated team items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_teams', '#__teams', 'com_teams.team', $item->id, 'id', '', '');

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		// Load item tags
		if (!empty($item->id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->id, 'com_teams.team');
		}
		$item->org_donation=TeamsModelTeam::getOrgsdonation();
		$item->activities=TeamsModelTeam::getActivities();

		return $item;
	}
	
		public function getOrgsdonation($pk = null)
		{
			$db	= JFactory::getDBO();	
			$query = " SELECT org_donation "
					." FROM `#__teams` AS a "
					." WHERE a.id='".intval(@$_REQUEST['id'])."'  ";

			$db->setQuery( $query );
			$rows = $db->loadObjectList();	
			foreach($rows as $row){
				$interested=explode(",", $row->org_donation);
			}
			//print_r($interested);
			//die;
			return @array_filter($interested);
		}

		public function getActivities($pk = null)
		{
			$db	= JFactory::getDBO();	
			$query = " SELECT activities "
					." FROM `#__teams` AS a "
					." WHERE a.id='".intval(@$_REQUEST['id'])."'  ";

			$db->setQuery( $query );
			$rows = $db->loadObjectList();	
			foreach($rows as $row){
				$interested=explode(",", $row->activities);
			}
			return @array_filter($interested);
		}		

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_teams.edit.team.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('team.id') == 0)
			{
				$app = JFactory::getApplication();
			}
		}

		$this->preprocessData('com_teams.team', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since    3.0
	 */
	public function save($data)
	{
		include('components/com_teams/Thumbnailer.premium.php');
		$thumbWidth=185;
		$thumbHeight=185;
		$realPath=str_replace("administrator", "", JPATH_BASE);
		$thumbPath=$realPath."images/team_logos/thumbs";


		$input = JFactory::getApplication()->input;

		// Alter the name for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));
			
			if ($data['name'] == $origTable->name)
			{
				list($name, $alias) = $this->generateNewTitle('',$data['name'], $data['alias']);
				$data['name'] = $name;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['published'] = 0;
		}
		
		//$data['alias']=JApplication::stringURLSafe($data['alias']);
		//$data['alias']='dddd';
		if (parent::save($data))
		{
			$config = JFactory::getConfig();
			$id = (int) $this->getState($this->getName() . '.id');
			if (!file_exists($config->get( 'abs_path' ).'/images/team_photos/'.$id)) {
				mkdir($config->get( 'abs_path' ).'/images/team_photos/'.$id, 0777);
			}
			if (!file_exists($config->get( 'abs_path' ).'/images/team_files/'.$id)) {
				mkdir($config->get( 'abs_path' ).'/images/team_files/'.$id, 0777);
			}			

			$assoc = JLanguageAssociations::isEnabled();

			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, 'Problem with associations');
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_teams.team'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);

					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_teams.team') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);

						return false;
					}
				}
			}

			if ($data['id'] == ''){
				$data['id'] = (int) $this->getState($this->getName() . '.id');
			}
			
			//print_r($data['org_donation']);
			//print_r(array_values($data['org_donation']));
			//$data['org_donation']=json_encode(array_values($data['org_donation']), JSON_FORCE_OBJECT);
			
		//{"catid":["","2","10"]}
			//$data['org_donation']='{"org_donation":'.json_encode($data['org_donation']).'}';
			$data['org_donation']=implode(",", $data['org_donation']).",";
			$data['activities']=implode(",", $data['activities']).",";
			
			//print_r($data);
			//echo $data['org_donation'];
			//die;
			parent::save($data);
			if ($handle = opendir($thumbPath) && trim($data['logo'])!=''){
								
				$filename = $realPath.$data['logo'];
				//echo $filename;
				//die;
				if (file_exists($filename)) {
					$size = getimagesize($filename);					
				
					if ($size['mime'] == "image/gif" || $size['mime'] == "image/jpeg" || $size['mime'] == "image/png"){
						$dotsArray=explode(".", $data['logo']);
						$thumbUrl=$thumbPath."/".$data['id'].".".$dotsArray[sizeof($dotsArray)-1];
					
						copy($filename, $thumbUrl);
						
						$thumb=new Thumbnailer($thumbUrl);
						$info1 = getimagesize($thumbUrl);
						
						//$thumbHeight=(int)($thumbWidth*$info1[1]/$info1[0]);
						
						$thumb->thumbFixed($thumbWidth,$thumbHeight)->save($thumbUrl,100);

						//$data['thumbWidth']=intval($thumbWidth);
						//$data['thumbHeight']=intval($thumbHeight);
						//$data['imageWidth']=intval($info1[0]);
						//$data['imageHeight']=intval($info1[1]);
						parent::save($data);
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The JTable object
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		$table->generateAlias();

		if (empty($table->id))
		{
			// Set the values
			$table->created = $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__teams'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified = $date->toSql();
			$table->modified_by = $user->get('id');
		}
		// Increment the content version number.
		//$table->version++;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();

		return $condition;
	}

	/**
	 * Preprocess the form.
	 *
	 * @param   JForm   $form   Form object.
	 * @param   object  $data   Data object.
	 * @param   string  $group  Group name.
	 *
	 * @return  void
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_CONTACT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_team');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the parent.
	 * @param   string   $alias        The alias.
	 * @param   string   $name         The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since   3.1
	 */
	protected function generateNewTitle($catid,$name, $alias)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias)))
		{
			if ($name == $table->name)
			{
				$name = JString::increment($name);
			}

			$alias = JString::increment($alias, 'dash');
		}

		return array($name, $alias);
	}
	
/*	public function __construct($db)
	{
		parent::__construct('#__teams', 'id', $db);
	}
 
	public function bind($array, $ignore = '')
	{
		// Bind the rules. 
		if (isset($array['rules']) && is_array($array['rules'])) { 
			$rules = new JRules($array['rules']); 
			$this->setRules($rules); 
		}
		return parent::bind($array, $ignore);
	}

        protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_teams.team.'.(int) $this->$k;
        }
 
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_teams');
		return $asset->id;
	}	*/
}
