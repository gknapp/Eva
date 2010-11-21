<?php

abstract class Event_Base {

	protected $bot;
	protected $response;
	
	public function __construct($bot) {
	
		$this->bot = $bot;
	
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