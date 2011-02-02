<?php

// After receiving Message of the day, join channels in $config['channels']
// Alternatively, if instructed by bot admin, join channel

class Event_Join extends Event_Base {

	private $motd = false;

	public function respondsTo($input) {
		// Once we've received MOTD end, request to join channels
		$event = $this->bot->receives($input)->raw(':[^\s]+ 376 ', false);
		
		if ($event) {
			$this->motd = true;
		} else {
			// If not MOTD check if it's a !join request 
			$event = $this->bot->receives($input)->fromAdmin()->match(
				'!join\s+(#[a-z0-9-]+)'
			);
		}
		
		return $event;
	}
	
	public function run($event) {
		if ($this->motd) {
			foreach ($this->bot->cfg['server.channels'] as $channel) {
				$this->bot->join($channel);
			}
			$this->motd = null;
		} elseif (!empty($event->matches[1])) {
			$this->bot->join($event->matches[1]);
		}
	}

}