<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_support
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
 
/**
 * Support Table class
 *
 * @since  0.0.1
 */
class SupportTableSupport extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__team_donation_types', 'id', $db);
	}
}