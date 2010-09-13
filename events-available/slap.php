<?php

// slaps someone in #channel
// !slap nick

class Event_Slap extends Event_Base {

	protected $_channel;
	protected $_target;

	public function respondsTo($response) {
	
		// PRIVMSG #channel :!slap eva
		$result = preg_match(
			'/PRIVMSG #([^\s]+) :!slap\s(\w+)/i',
			$response,
			$matches
		);
		
		if ($result) {
			list(, $this->_channel, $this->_target) = $matches;
		}
		
		return $result;
	
	}
	
	public function run() {
	
		$this->bot->client->action(
			"slaps {$this->_target} around with a cricket bat",
			$this->_channel
		);
	
	}

}