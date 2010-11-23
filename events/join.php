<?php

// After receiving Message of the day, join channels in $config['channels']

class Event_Join extends Event_Base {

	public function respondsTo($event) {
	
		// Once we've received MOTD end request to join channels
		return preg_match('/:[^\s]+ 376 /', $event);
	
	}
	
	public function run() {
	
		foreach ($this->bot->cfg['server.channels'] as $channel) {
			$this->bot->join($channel);
		}
	
	}

}