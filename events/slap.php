<?php

// slaps someone in #channel
// !slap nick

class EventSlap extends EventBase {

	protected $_channel;
	protected $_target;

	public function respondsTo($response) {
	
		// PRIVMSG #gravediggas :!slap eva
		$result = preg_match(
			'/PRIVMSG #([^\s]+) :!slap\s(\w+)/',
			$response,
			$matches
		);
		
		if ($result) {
			list(, $this->_channel, $this->_target) = $matches;
		}
		
		return $result;
	
	}
	
	public function run($client, $config) {
	
		$client->action(
			"slaps {$this->_target} around with a cricket bat",
			$this->_channel
		);
	
	}

}