<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_stats
 * @since       1.6
 */
class StatsControllerStats extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  ContactControllerContacts
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}


	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Stats', $prefix = 'StatsModel', $config = array('ignore_request' => true))
	{
		//$model = parent::getModel($name, $prefix, $config);

		//return $model;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
	
	
	public function unpublish()
	{
	}
	
	public function publish($pks, $value = 1)
	{
	}
	
	public function trash($pks, $value = 1)
	{
	}
	
	
	public function delete()
	{
	}

}
