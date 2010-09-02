<?php

class ircClient {

	public $channels = array();

	protected $_connection;
	protected $_nickname;
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

	public function say($msg, $channel) {
	
		$channel = $this->_prefixChannel($channel);
		$this->raw("PRIVMSG $channel $msg");
	
	}
	
	public function action($msg, $channel) {
	
		$this->say(chr(1) . "ACTION $msg ", $this->_prefixChannel($channel));
	
	}
	
	public function oper() {
	
		$this->raw("OPER {$this->_config['nickname']} {$this->_config['password']}");
	
	}
	
	public function quit($msg = 'bye') {
	
		$this->raw("QUIT :$msg");
	
	}
	
	protected function _isMotd($response) {
	
		return (strpos($response, ' 376 ') !== false);
	
	}

	protected function _login($config) {
	
		if ($this->_connection->connected()) {
			if (array_key_exists('password', $config)) {
				$this->_connection->send("PASS {$config['password']}");
			}
			
			$this->setNickname($config['nickname']);
			$this->raw(
				"USER {$config['nickname']} {$config['hostname']} {$config['nickname']} :{$config['nickname']}"
			);
			
			$this->_config = $config;
		}
	
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
