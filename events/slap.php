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
	
		$nickname = $this->_getVictim($event);
		$item = 'frying pan';
		
		if ($this->_hasItemSpecified($event)) {
			$item = $this->_getItem($event);
			$item = $this->_analyseGrammar($item);
		} else {
			$item = 'a ' . $item;
		}
		
		$this->bot->action(
			"slaps $nickname around with $item", $event->target
		);
	
	}
	
	private function _getVictim($event) {
		return $event->matches[1];
	}
	
	private function _hasItemSpecified($event) {
		return !empty($event->matches[2]);
	}
	
	private function _getItem($event) {
		return $event->matches[2];
	}
	
	private function _analyseGrammar($item) {
		$prefix = 'a ';
		
		list($word, $rest) = explode(' ', $item, 2);
		$word = strtolower($word);
		
		// strip 'a' prefix
		if (in_array($word, array('with','my','a','an'))) {
			$prefix = '';
		}
		
		
		if ($word != 'with') {
			$rest = $item;
		}
		
		// substitute 'my' with author's nick
		$rest = str_replace('my ', $event->nick . "'s ", $rest);
		$item = $prefix . $rest;
		
		return $item;
	}

}