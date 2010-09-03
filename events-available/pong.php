<?php

// send PONG! to PING? request or get disconnected

class EventPong extends EventBase {

	protected $_response;

	public function respondsTo($response) {
	
		$this->_response = $response;
		return (substr($response, 0, 6) == 'PING :');
	
	}
	
	public function run($client) {
	
		$client->raw('PONG :' . substr($this->_response, 6));
	
	}

}