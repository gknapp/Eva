<?php

class ircBot {

	protected $_listeners = array();
	protected $_client;

	public function __construct(ircClient $client) {
	
		$this->_client = $client;
		
		// register key listeners
		$this->addListener(new EventPong);
	
	}
	
	// Add event listener
	public function addListener(EventBase $listener) {
	
		$this->_listeners[] = $listener;
	
	}
	
	protected function _onTick($response, $config) {
	
		foreach ($this->_listeners as $event) {
			if ($event->respondsTo($response)) {
				$event->run($this->_client, $config);
			}
		}
	
	}
	
	public function run($config) {
	
		$this->_client->connect($config);
	
		while ($this->_client->connected()) {
			$response = $this->_client->readLine();
			echo $response; // debug			
			$this->_onTick($response, $config);
		}
	
	}

}