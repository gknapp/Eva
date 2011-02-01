<?php

class IrcConnection {

	public $timeout = 5; // 5 second connection timeout
	protected $_conn; // connection handle
	
	public function connect($server, $port) {
		$conn = @fsockopen($server, $port, $err, $errStr, $this->timeout);
		
		if (!$conn) {
			die("Connection failed to {$server}:{$port} (" . $errStr . ")\n");
		}
		
		$this->_conn = $conn;
		return true;
	}
	
	public function send($cmd) {
		if (!$this->_conn) {
			die("No connection established, cannot send command");
			return false;
		}
		
		echo "$cmd\r\n";
		return fwrite($this->_conn, $cmd . "\r\n", strlen($cmd) + 2);
	}
	
	public function connected() {
		return ($this->_conn && !feof($this->_conn));
	}
	
	public function readLine() {
		if ($this->_conn) {
			return fgets($this->_conn, 512);
		} else {
			die("Connection lost\n");
		}
	}
	
	public function close() {
		if ($this->_conn) {
			fclose($this->_conn);
			$this->_conn = false;
		}
	}
	
	public function __destruct() {
		$this->close();
	}

}
