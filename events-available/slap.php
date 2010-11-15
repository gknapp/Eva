<?php

// slaps someone in #channel
// !slap nick

class Event_Slap extends Event_Base {

	/**
	 * Do I respond to this event?
	 */
	public function respondsTo($event) {
	
		// !slap Fred
		$this->_response = $this->_bot
								->receives($event)
								->inChannel()
								->match("!slap (\w+)");
		
		return $this->_response;
	
	}
	
	/**
	 * Perform action in response to event
	 */
	public function run() {
	
		$nickname = $this->_response->nick;
	
		$this->_bot->action(
			"slaps $nickname around with a frying pan",
			$this->_response->target
		);
	
	}

}