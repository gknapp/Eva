<?php

// identify with NickServ:
// "This nickname is registered and protected.  If it is your \
// please choose a different nick"

class Event_Identify extends Event_Base {

	protected $_target;

	public function respondsTo($response) {
	
		$result = preg_match(
			'/:([^!]+)![^\s]+\sNOTICE\s\w+\s\:please choose a different nick/',
			$response,
			$target
		);
		
		if ($result) {
			$this->_target = array_pop($target);
		}
		
		return $result;
	
	}
	
	public function run() {
	
		$cfg = $this->_bot->cfg;
		$this->_bot->client->raw(
			"PRIVMSG {$this->_target} IDENTIFY {$cfg['nickpswd']}"
		);
	
	}

}