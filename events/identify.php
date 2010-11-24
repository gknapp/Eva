<?php

/**
 * Event to identify with NickServ
 *
 * "This nickname is registered and protected. If it is your please choose 
 *  a different nick"
 *
 * @ingroup Events
 */

class Event_Identify extends Event_Base {

	public function respondsTo($event) {
	
		$nickserv = $this->bot->cfg['ns.nick'];
		$event = $this->bot->receives($event)->asNotice();
		
		$this->response = $event->from($nickserv)->match(
			'This nickname is registered and protected'
		);
		
		return $this->response;
	
	}
	
	public function run() {
		
		$this->bot->privateMessage(
			'IDENTIFY ' . $this->bot->cfg['ns.pswd'],
			$this->bot->cfg['ns.nick']
		);
	
	}

}