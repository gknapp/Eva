<?php

// After receiving Message of the day, join channels in $config['channels']
// Alternatively, if instructed by bot admin, join channel

class Event_Join extends Event_Base {

	protected $_endMotd;

	public function respondsTo($event) {
		
		// Once we've received MOTD end, request to join channels
		$this->_endMotd = $this->bot->receives($event)->raw(
			':[^\s]+ 376 ', false
		);
		
		if (!$this->_endMotd) {
			// If not MOTD check if it's a !join request 
			return $this->bot->receives($event)->fromAdmin()->match(
				'!join\s+(#[a-z0-9-]+)'
			);
		}
		
		return $this->_endMotd;
	
	}
	
	public function run($event) {
	
		if ($this->_endMotd) {
			foreach ($this->bot->cfg['server.channels'] as $channel) {
				$this->bot->join($channel);
			}
		} elseif (isset($event->matches[1])) {
			$this->bot->join($event->matches[1]);
		}
	
	}

}