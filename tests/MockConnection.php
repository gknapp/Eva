<?php

class MockConnection {

	private $_response;

	public function connect($server, $port) {
		return true;
	}
	
	public function connected() {
		return true;
	}
	
	public function send($cmd) {
		$this->_response = $cmd;
		return 1;
	}
	
	public function readLine() {}
	public function close() {}
		
	public function readResponse() {
		return $this->_response;
	}
	
	public function clearResponse() {
		$this->_response = '';
	}

}

