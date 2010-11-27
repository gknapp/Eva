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
		return $event->inChannel()->match("!slap (\w+)( .*)?");
	
	}
	
	/**
	 * Perform action in response to event
	 */
	public function run($event) {
	
		$nickname = $event->matches[1];
		$item = 'frying pan';
		
		if (!empty($event->matches[2])) {
			$item = $event->matches[2];
			$prefix = 'a ';
			
			// small bit of analysis on the item
			list($word, $rest) = explode(' ', $item, 2);
			$word = strtolower($word);
			
			// strip 'a' prefix
			if ($word == 'with' || $word == 'my' || $word = 'a') {
				$prefix = '';
			}
			
			if ($word != 'with') {
				$rest = $item;
			}
			
			// substitute 'my' with author's nick
			$rest = str_replace('my ', $event->nick . "'s ", $rest);
			$item = $prefix . $rest;
		}
		
		$this->bot->action(
			"slaps $nickname around with $item", $event->target
		);
	
	}

}