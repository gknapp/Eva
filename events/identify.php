<?php

// identify with NickServ:
// "This nickname is registered and protected. If it is your please choose 
//  a different nick"

class Event_Identify extends Event_Base {

	public function respondsTo($event) {
	
		$this->response = $this->bot->receives($event)->match(
			'This nickname is registered and protected'
		);
		
		return $this->response;
	
	}
	
	public function run() {
		
		$this->bot->privateMessage(
			'IDENTIFY ' . $this->bot->cfg['nickserv.pswd'],
			$this->response->nick
		);
	
	}

}