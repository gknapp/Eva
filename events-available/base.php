<?php

abstract class EventBase {

	protected $_cfg; // config

	abstract public function respondsTo($response);
	abstract public function run($client);
	
	public function setConfig($config) {
	
		$this->_cfg = $config;
	
	}

}