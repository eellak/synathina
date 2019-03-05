<?php
/**
 * @package     Core.Administrator
 * @subpackage  com_teams
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Core.Administrator
 * @subpackage  com_teams
 * @since       1.6
 */
class JFormFieldTeamEdit extends JFormFieldList
{
	/**
	 * A flexible team list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'TeamEdit';

	/**
	 * Method to get a list of teams that respects access controls and can be used for
	 * either team assignment or parent team assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent teams.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();
		$where='';
		$db = JFactory::getDbo();
		$new_lang = $this->form->getData(true)->get('language');
		if (!empty($new_lang))		{
			$where=" AND (c.language='".$new_lang."' OR c.language='*')  ";
		}		
		$query = "SELECT c.id AS value,c.name AS text FROM #__teams AS c WHERE c.published=1 ".$where;
		$db->setQuery($query);
		$options = $db->loadObjectList();
		//print_r($options);
		//die;
		return $options;
		/*$options = array();
		$published = $this->element['published'] ? $this->element['published'] : array(0, 1);
		$name = (string) $this->element['name'];

		// Let's get the id for the current item, either team or content item.
		$jinput = JFactory::getApplication()->input;
		// Load the team options for a given extension.

		// For teams the old team is the team id or 0 for new team.
		if ($this->element['parent'] || $jinput->get('option') == 'com_teams')
		{
			$oldCat = $jinput->get('id', 0);
			$oldParent = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('extension', 'com_content');
		}
		else
			// For items the old team is the team they are in when opened or 0 if new.
		{
			$oldCat = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('option', 'com_content');
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.name AS text, a.published')
			->from('#__teams AS a')
			->join('LEFT', $db->quoteName('#__teams') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// If parent isn't explicitly stated but we are in com_teams assume we want parents
		if ($oldCat != 0 && ($this->element['parent'] == true || $jinput->get('option') == 'com_teams'))
		{
			// Prteam parenting to children of this item.
			// To rearrange parents and children move the children up, not the parents down.
			$query->join('LEFT', $db->quoteName('#__teams') . ' AS p ON p.id = ' . (int) $oldCat)
				->where('');

			$rowQuery = $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.nae AS text ')
				->from('#__teams AS a')
				->where('a.id = ' . (int) $oldCat);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}

		// Filter language
		if (!empty($this->element['language']))
		{

			$query->where('a.language = ' . $db->quote($this->element['language']));
		}

		// Filter on the published state

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			JArrayHelper::toInteger($published);
			$query->where('a.published IN (' . implode(',', $published) . ')');
		}

		$query->group('a.id, a.name, a.published')
			->order('a.name ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if ($this->element['parent'] == true || $jinput->get('option') == 'com_teams')
			{
				if ($options[$i]->level == 0)
				{
					$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
				}
			}
			if ($options[$i]->published == 1)
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			}
			else
			{
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		}

		// Get the current user object.
		$user = JFactory::getUser();

		// For new items we want a list of teams you are allowed to create in.
		if ($oldCat == 0)
		{
			foreach ($options as $i => $option)
			{
				// To take save or create in a team you need to have create rights for that team
				// unless the item is already in that team.
				// Unset the option if the user isn't authorised for it. In this field assets are always teams.
				if ($user->authorise('core.create', $extension . '.team.' . $option->value) != true)
				{
					unset($options[$i]);
				}
			}
		}
		// If you have an existing team id things are more complex.
		else
		{
			// If you are only allowed to edit in this team but not edit.state, you should not get any
			// option to change the team parent for a team or the team for a content item,
			// but you should be able to save in that team.
			foreach ($options as $i => $option)
			{
				if ($user->authorise('core.edit.state', $extension . '.team.' . $oldCat) != true && !isset($oldParent))
				{
					if ($option->value != $oldCat)
					{
						unset($options[$i]);
					}
				}
				if ($user->authorise('core.edit.state', $extension . '.team.' . $oldCat) != true
					&& (isset($oldParent))
					&& $option->value != $oldParent
				)
				{
					unset($options[$i]);
				}

				// However, if you can edit.state you can also move this to another team for which you have
				// create permission and you should also still be able to save in the current team.
				if (($user->authorise('core.create', $extension . '.team.' . $option->value) != true)
					&& ($option->value != $oldCat && !isset($oldParent))
				)
				{
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create', $extension . '.team.' . $option->value) != true)
					&& (isset($oldParent))
					&& $option->value != $oldParent
				)
				{
					{
						unset($options[$i]);
					}
				}
			}
		}
		if (($this->element['parent'] == true || $jinput->get('option') == 'com_teams')
			&& (isset($row) && !isset($options[0]))
			&& isset($this->element['show_root'])
		)
		{
			if ($row->parent_id == '1')
			{
				$parent = new stdClass;
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;*/
	}
}
