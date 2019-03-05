<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Contact Table class.
 *
 * @since  1.0
 */
class StegihoursTableStegihour extends JTable
{
	/**
	 * Ensure the params and metadata in json encoded in the bind method
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $_jsonEncode = array('params', 'metadata');

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  Database connector object
	 *
	 * @since   1.0
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__stegihours', 'id', $db);

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_stegihours.stegihour'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_stegihours.stegihour'));
	}

	/**
	 * Stores a contact.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params))
		{
			$registry = new Registry;
			$registry->loadArray($this->params);
			$this->params = (string) $registry;
		}

		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		$this->modified		= $date->toSql();

		if ($this->id)
		{
			// Existing item
			$this->modified_by	= $user->get('id');
		}
		else
		{
			// New contact. A contact created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}


		// Verify that the alias is unique
		$table = JTable::getInstance('Stegihour', 'StegihoursTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError('error in alias');

			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see JTable::check
	 * @since 1.5
	 */
	public function check()
	{
		//$this->default_con = (int) $this->default_con;

		/** check for valid name */
		if (trim($this->name) == '')
		{
			$this->setError('Provide a valid last name');

			return false;
		}

		// Generate a valid alias
		$this->generateAlias();

		/*
		 * Clean up keywords -- eliminate extra spaces between phrases
		 * and cr (\r) and lf (\n) characters from string.
		 * Only process if not empty.
 		 */
		if (!empty($this->metakey))
		{
			// Array of characters to remove.
			$bad_characters = array("\n", "\r", "\"", "<", ">");

			// Remove bad characters.
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);

			// Create array using commas as delimiter.
			$keys = explode(',', $after_clean);
			$clean_keys = array();

			foreach ($keys as $key)
			{
				// Ignore blank keywords.
				if (trim($key))
				{
					$clean_keys[] = trim($key);
				}
			}

			// Put array back together delimited by ", "
			$this->metakey = implode(", ", $clean_keys);
		}

		// Clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc))
		{
			// Only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}

		return true;
	}

	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @return  string
	 */
	public function generateAlias()
	{
		if (empty($this->alias))
		{
			$this->alias = $this->name;
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
