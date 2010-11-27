<?php

abstract class Event_Base {

	protected $bot;
	
	public function __construct($bot) {
	
		$this->bot = $bot;
	
	}
	
	/**
	 * Method to check event listeners requirements are met
	 */
	public function satisfied() {
	
		return true;
	
	}

	abstract public function respondsTo($event);
	abstract public function run($event);
	
}