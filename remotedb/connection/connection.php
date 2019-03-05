<?php

class RemotedbConnection
{

	protected $options;

	public function __construct()
	{
		$config = JFactory::getConfig();
		$this->options = [];
		$this->options['driver']   = $config->get('dbtype');
		$this->options['host']     = $config->get('common_host');
		$this->options['user']     = $config->get('common_user');
		$this->options['password'] = $config->get('common_password');
		$this->options['database'] = $config->get('common_db');
		$this->options['prefix']   = $config->get('common_dbprefix');
		$this->remotoptions = [];
		$this->remotoptions['driver']   = $config->get('dbtype');
		$this->remotoptions['host']     = $config->get('remote_host');
		$this->remotoptions['user']     = $config->get('remote_user');
		$this->remotoptions['password'] = $config->get('remote_password');
		$this->remotoptions['database'] = $config->get('remote_db');
		$this->remotoptions['prefix']   = $config->get('remote_dbprefix');
	}

	public function connect()
	{
		if ($db_remote = JDatabase::getInstance($this->options)) {
			return $db_remote;
		} else {
			echo 'Could not connect to remote db';
			die;
		}
	}

	public function remoteConnect()
	{
		if ($db_remote = JDatabase::getInstance($this->remotoptions)) {
			return $db_remote;
		} else {
			echo 'Could not connect to remote db';
			die;
		}
	}

}