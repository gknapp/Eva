<?php

// send PONG! to PING? request or get disconnected

class Event_Pong extends Event_Base {

	protected $_response;

	public function respondsTo($response) {
	
		$this->_response = $response;
		return (substr($response, 0, 6) == 'PING :');
	
	}
	
	public function run() {
	
		$this->_bot->client->raw('PONG :' . substr($this->_response, 6));
	
	}

}