<?php

// send PONG! to PING? request or get disconnected

class Event_Pong extends Event_Base {

	public function respondsTo($event) {
	
		return $this->bot->receives($event)->raw('PING :(.*)', false);
	
	}
	
	public function run($event) {
	
		$this->bot->client->raw('PONG :' . $event->matches[1]);
	
	}

}