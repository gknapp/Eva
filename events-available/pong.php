<?php

// send PONG! to PING? request or get disconnected

class Event_Pong extends Event_Base {

	public function respondsTo($event) {
	
		$this->response = $event;
		return (substr($event, 0, 6) == 'PING :');
	
	}
	
	public function run() {
	
		$this->bot->client->raw('PONG :' . substr($this->response, 6));
	
	}

}