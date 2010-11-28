<?php

// Parts a specified #channel requested by a bot admin.
// !leave #channel

class Event_Part extends Event_Base {

	public function respondsTo($event) {
	
		return $this->bot->receives($event)->fromAdmin()->match(
			'!leave\s?(#[a-z0-9]+)?'
		);
	
	}
	
	public function run($event) {
	
		// leave current channel if no channel is specified
		$channel = $event->target;
		
		if (!empty($event->matches[1])) {
			$channel = $event->matches[1];
		}
		
		$this->bot->part($channel);
	
	}
	
}