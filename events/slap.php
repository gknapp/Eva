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
		
		if (!empty($this->response->matches[2])) {
			$item = $this->response->matches[2];
			$prefix = 'a ';
			
			// small bit of analysis on the item
			list($word, $rest) = explode(' ', strtolower($item), 2);
			
			// strip 'a' prefix
			if ($word == 'with' || $word == 'my' || $word = 'a') {
				$prefix = '';
			}
			
			if ($word != 'with') {
				$rest = $item;
			}
			
			// substitute 'my' with author's nick
			$rest = str_replace('my', $this->response->nick . "'s", $rest);
			$item = $prefix . $rest;
		}
		
		$this->bot->action(
			"slaps $nickname around with $item",
			$this->response->target
		);
	
	}

}