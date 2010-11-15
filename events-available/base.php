<?php

abstract class Event_Base {

	protected $_bot;
	protected $_response;
	
	public function __construct($bot) {
	
		$this->_bot = $bot;
	
	}
	
	/**
	 * Method to check event listeners requirements are met
	 */
	public function satisfied() {
	
		return true;
	
	}

	abstract public function respondsTo($response);
	abstract public function run();
	
}