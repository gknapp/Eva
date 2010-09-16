<?php

class ircClient {

	protected $_connection;
	protected $_config;

	public function connect($config) {
	
		$this->_connection = new ircConnection;
		
		if (!$this->_connection->connect($config)) {
			die("Failed to connect.\n");
		}
		
		$this->_login($config);
	
	}
	
	public function connected() {
	
		return ($this->_connection && $this->_connection->connected());
	
	}
	
	public function readLine() {
	
		return $this->_connection->readLine();
	
	}

	public function raw($response) {
	
		$this->_connection->send($response);
	
	}

	public function setNickname($nickname, $original = null) {
	
		if ($original) {
			// changing nickname
			$this->raw(":$original NICK $nickname");
		} else {
			// introducing new nickname
			$this->raw("NICK $nickname");
		}
	
	}
	
	public function join($channel) {
	
		$this->raw("JOIN " . $this->_prefixChannel($channel));
	
	}
	
	public function part($channel) {
	
		$this->raw("PART " . $this->_prefixChannel($channel));
	
	}

	public function say($msg, $target) {
	
		$this->raw("PRIVMSG $target :$msg");
	
	}
	
	public function notice($msg, $target) {
	
		$this->raw("NOTICE $target :$msg");
	
	}
	
	public function action($msg, $target) {
	
		$this->say(chr(1) . "ACTION $msg ", $target);
	
	}
	
	public function quit($msg = 'bye') {
	
		$this->raw("QUIT :$msg");
	
	}
	
	protected function _login($config) {
	
		$this->_config = $config;
	
		while ($this->_connection->connected()) {
			$line = $this->_connection->readLine();
			
			if (strpos($line, ' NOTICE AUTH ') !== false &&
				strpos($line, 'Found your hostname') !== false) {
				if (array_key_exists('password', $config)) {
					$this->_connection->send("PASS {$config['password']}");
				}
				
				$this->setNickname($config['nickname']);
				$this->_auth($config);
				break;
			}
		}
	
	}
	
	protected function _auth($config) {
	
		$this->raw(
			"USER {$config['nickname']} {$config['hostname']} " .
			"{$config['nickname']} :{$config['nickname']}"
		);
	
	}
	
	protected function _prefixChannel($channel) {
	
		return !in_array($channel[0], array('#','&')) ? "#$channel" : $channel;
	
	}
	
	public function __destruct() {
	
		if ($this->_connection && $this->_connection->connected()) {
			$this->_connection->close();
		}
	
	}

}
