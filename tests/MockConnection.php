<?php

class MockConnection {

	public function connect($server, $port) {
		return true;
	}
	
	public function connected() {
		return true;
	}
	
	public function send($cmd) {
		echo $cmd;
		return 1;
	}
	
	public function readLine() {}
	
	public function close() {}

}

