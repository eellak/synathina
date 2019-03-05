<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Methods supporting a list of contact records.
 *
 * @since  1.6
 */
class ActionsModelActions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'catid', 'a.catid',
				'checked_out', 'a.checked_out',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'published', 'a.published',
				'description', 'a.description'
			);

			$assoc = JLanguageAssociations::isEnabled();

			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$exhibitionId = $this->getUserStateFromRequest($this->context . '.filter.exhibition_id', 'filter_exhibition_id');
		$this->setState('filter.exhibition_id', $exhibitionId);

		$annexeId = $this->getUserStateFromRequest($this->context . '.filter.annexe_id', 'filter_annexe_id');
		$this->setState('filter.annexe_id', $annexeId);

		$painterId = $this->getUserStateFromRequest($this->context . '.filter.painter_id', 'filter_painter_id');
		$this->setState('filter.painter_id', $painterId);
		
		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
	

		// Force a language.
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.exhibition_id');
		$id .= ':' . $this->getState('filter.annexe_id');
		$id .= ':' . $this->getState('filter.painter_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
				explode(', ', $this->getState(
					'list.select',
					'a.id, a.name, a.language, a.ordering, a.alias, a.published, a.title, a.description, a.catid, a.checked_out, a.created, a.created_by, a.publish_up, a.publish_down, a.team_id'
					)
				)
			)
		);
		$query->from($db->quoteName('#__actions', 'a'));

		// Join over the language
		$query->select($db->quoteName('l.title', 'language_title'))
			->join(
				'LEFT', $db->quoteName('#__languages', 'l')
				. ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language')
			);
			
		

		// Join over the asset groups.
		$query->select($db->quoteName('ag.title', 'access_level'))
			->join(
				'LEFT', $db->quoteName('#__viewlevels', 'ag')
				. ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
			);


		// Join over the associations.
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$query->select('COUNT(' . $db->quoteName('asso2.id') . ') > 1 as ' . $db->quoteName('association'))
				->join(
					'LEFT', $db->quoteName('#__associations', 'asso')
					. ' ON ' . $db->quoteName('asso.id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('asso.context') . ' = ' . $db->quote('com_actions.action')
				)
				->join(
					'LEFT', $db->quoteName('#__associations', 'asso2')
					. ' ON ' . $db->quoteName('asso2.key') . ' = ' . $db->quoteName('asso.key')
				)
				->group(
					$db->quoteName(
						array(
							'a.id',
							'a.name',
							'a.alias',
							'l.title',
							'ag.title'
						)
					)
				);
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access') . ' = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where($db->quoteName('a.access') . ' IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId))
		{
			$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
		}
		elseif (is_array($categoryId))
		{
			Joomla\Utilities\ArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where($db->quoteName('a.catid') . ' IN (' . $categoryId . ')');
		}

		// Filter on the exhibition_id.
		$exhibitionId = $this->getState('filter.exhibition_id');
		if (is_numeric($exhibitionId)) {
			$query->where('a.exhibition_id = '.$db->quote($exhibitionId));
		}

		// Filter on the annexe_id.
		$annexeId = $this->getState('filter.annexe_id');
		if (is_numeric($annexeId)) {
			$query->where('a.annexe_id = '.$db->quote($annexeId));
		}

		// Filter on the exhibition_id.
		$painterId = $this->getState('filter.painter_id');
		if (is_numeric($painterId)) {
			$query->where('a.painter_id = '.$db->quote($painterId));
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where(
				'( '
				.$db->quoteName('a.name') . ' LIKE ' . $search 
				.' OR ' . $db->quoteName('a.alias') . ' LIKE ' . $search
				.' OR ' . $db->quoteName('a.name') . ' LIKE ' . $search
				.' )'
			);
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language') . ' = ' . $db->quote($language));
		}
		

		
		$query->where($db->quoteName('a.action_id') . ' = 0 ');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		return $query;
	}
	
	
	public function saveorder()
	{
		$db = JFactory::getDbo();
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks = $_REQUEST['cid'];
		$order = $_REQUEST['order'];
		
		$return=true;

		for ($i=0; $i<sizeof($order); $i++){
			$query = 'UPDATE #__actions '
			       . 'SET ordering = ' . intval( $order[$i] ) . ' '
			       . 'WHERE id =  '.$pks[$i].' ';
			
			$db->setQuery( $query );
			if (!$db->query()) {
				$return = false; 
			}
		}


		if ($return === false)
		{
			// Reorder failed
			//$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			//$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// Reorder succeeded.
			//$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			//$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
			return true;
		}
	}
}
