<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_supportersexports
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a supportersexport.
 *
 * @since  1.6
 */
class SupportersexportsViewSupportersexport extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
			//$this->form->setFieldAttribute('id', 'readonly', 'true');
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !(@$this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= JHelperContent::getActions('com_supportersexports', 'category', $this->item->id);

		JToolbarHelper::title('Supportersexport');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			//if ($isNew && (count($user->getAuthorisedCategories('com_supportersexports', 'core.create')) > 0))
			//{
				JToolbarHelper::apply('supportersexport.apply');
				JToolbarHelper::save('supportersexport.save');
				JToolbarHelper::save2new('supportersexport.save2new');
			//}

			JToolbarHelper::cancel('supportersexport.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
				{
					JToolbarHelper::apply('supportersexport.apply');
					JToolbarHelper::save('supportersexport.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('supportersexport.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('supportersexport.save2copy');
			}

			//if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			//{
			//	JToolbarHelper::versions('com_supportersexports.supportersexport', $this->item->id);
			//}

			JToolbarHelper::cancel('supportersexport.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_CONTACTS_CONTACTS_EDIT');
	}
}