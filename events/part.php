<?php

// Parts a specified #channel if requested by configuration
// or by a bot admin.
// !leave #channel

class Event_Part extends Event_Base {

	protected $_channel;
	protected $_target;

	public function respondsTo($event) {
	
		$event = $this->bot->receives($event)->match('!leave(\s#[a-z0-9]+)?');
		
		// only respond if admin issued command
		if ($event && !in_array($event->nick, $this->bot->getAdmins())) {
			$event = false;
		}
		
		return $event;
	
	}
	
	public function run($event) {
	
		// leave current channel if no channel is specified
		$channel = $event->target;
		
		if (isset($event->matches[1])) {
			$channel = $event->matches[1];
		}
		
		$this->bot->part($channel);
	
	}
	
}