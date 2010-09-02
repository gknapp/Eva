<?php

// Join channels in $config['channels']

class EventJoin extends EventBase {

	public function respondsTo($response) {
	
		return preg_match('/:[^\s]+ 376 /', $response, $target);
	
	}
	
	public function run($client, $config) {
	
		foreach ($config['channels'] as $channel) {
			$client->join($channel);
		}
	
	}

}