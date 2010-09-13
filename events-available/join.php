<?php

// Join channels in $config['channels']

class Event_Join extends Event_Base {

	public function respondsTo($response) {
	
		// Once we've received MOTD end request to join channels
		return preg_match('/:[^\s]+ 376 /', $response);
	
	}
	
	public function run() {
	
		foreach ($this->_bot->cfg['channels'] as $channel) {
			$this->_bot->client->join($channel);
		}
	
	}

}