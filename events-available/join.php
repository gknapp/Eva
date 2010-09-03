<?php

// Join channels in $config['channels']

class EventJoin extends EventBase {

	public function respondsTo($response) {
	
		// Once we've received MOTD end request to join channels
		return preg_match('/:[^\s]+ 376 /', $response);
	
	}
	
	public function run($client) {
	
		foreach ($this->_cfg['channels'] as $channel) {
			$client->join($channel);
		}
	
	}

}