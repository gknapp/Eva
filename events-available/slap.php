<?php

// Get the bot to slap someone in a #channel
// !slap nick
// Slap them with a trout
// !slap nick trout

class Event_Slap extends Event_Base {

	/**
	 * Do I respond to this event?
	 */
	public function respondsTo($event) {
	
		$event = $this->bot->receives($event);
		$this->response = $event->inChannel()->match("!slap (\w+)( .*)?");
		
		return $this->response;
	
	}
	
	/**
	 * Perform action in response to event
	 */
	public function run() {
	
		$nickname = $this->response->matches[1];
		$item = 'frying pan';
		
		if (isset($this->response->matches[2])) {
			$item = $this->response->matches[2];
		}
	
		$this->bot->action(
			"slaps $nickname around with a $item",
			$this->response->target
		);
	
	}

}