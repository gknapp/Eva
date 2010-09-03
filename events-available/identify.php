<?php

// identify with NickServ:
// This nickname is registered and protected.  If it is your
// please choose a different nick

class EventIdentify extends EventBase {

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
	
	public function run($client) {
	
		$client->raw("PRIVMSG {$this->_target} IDENTIFY {$this->_cfg['nickpswd']}");
	
	}

}