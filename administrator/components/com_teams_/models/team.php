<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * teams model.
 *
 * @package     Core.Administrator
 * @subpackage  com_teams
 * @since       1.5
 */
class TeamsModelTeam extends JModelAdmin
{

	/**
	 * The team alias for this content team.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $teamAlias = 'com_teams.team';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_TEAMS';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
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

			//if ($record->catid)
			//{
			//	return $user->authorise('core.delete', 'com_teams.category.'.(int) $record->catid);
			//}
			//else
			//{
				return parent::canDelete($record);
			//}
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		//if (!empty($record->catid))
		//{
		//	return $user->authorise('core.edit.state', 'com_teams.category.'.(int) $record->catid);
		//}
		//else
		//{
			return parent::canEditState($record);
		//}
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $team    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($team = 'Team', $prefix = 'TeamsTable', $config = array())
	{
		return JTable::getInstance($team, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
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
		// Get the form.
		$form = $this->loadForm('com_teams.team', 'team', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		//if ($this->getState('team.id'))
		//{
			// Existing record. Can only edit in selected categories.
		//	$form->setFieldAttribute('catid', 'action', 'core.edit');
		//}
		//else
		//{
			// New record. Can only create in selected categories.
			//$form->setFieldAttribute('catid', 'action', 'core.create');
		//}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			//$form->setFieldAttribute('publish_up', 'disabled', 'true');
			//$form->setFieldAttribute('publish_down', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			//$form->setFieldAttribute('publish_up', 'filter', 'unset');
			//$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
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
				//$data->set('catid', $app->input->get('catid', $app->getUserState('com_teams.teams.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData('com_teams.team', $data);

		return $data;

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
			// Convert the metadata field to an array.
			$registry = new JRegistry;
			//$registry->loadString($item->metadata);
			//$item->metadata = $registry->toArray();

			// Convert the images field to an array.
			$registry = new JRegistry;
			//$registry->loadString($item->images);
			//$item->images = $registry->toArray();

			if (!empty($item->id))
			{
				//$item->tags = new JHelperTags;
				//$item->tags->getTagIds($item->id, 'com_teams.team');
				//$item->metadata['tags'] = $item->tags;
			}
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = JApplication::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplication::stringURLSafe($table->title);
		}

		if (empty($table->id))
		{
			// Set the values

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__teams');
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
			else
			{
				// Set the values
				//$table->modified    = $date->toSql();
				//$table->modified_by = $user->get('id');
			}
		}

		// Increment the team version number.
		//$table->version++;

	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since	3.1
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
	
		$data['title']=$data['name'];
		$data['alias'] = $this->generateAlias($data['title']);		
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		//die;
		
		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle('', $data['alias'], $data['name']);
			
			//list($name, $alias) = '';
			$data['name']	= $name;
			$data['alias'] = $this->generateAlias($data['title']);
			$data['published']	= 0;
	
		}

		return parent::save($data);
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
	protected function generateNewTitle($category_id, $alias, $name)
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
	public function generateAlias($name)
	{
		if (empty($this->alias))
		{
			$this->alias = $name;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		$this->alias=str_replace(":", "-", $this->alias);

		return $this->alias;
	}	
}
