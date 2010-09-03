<?php

class ircBot {

	protected $_listeners = array();
	protected $_client;
	protected $_cfg;

	public function __construct(ircClient $client, $config) {
	
		$this->_client = $client;
		$this->_cfg = $config;
		
	}
	
	// Add event listener
	public function addListener(EventBase $listener) {
	
		$listener->setConfig($this->_cfg);
		$this->_listeners[] = $listener;
	
	}

	public function loadEvents($path) {

		$required = array('EventPong', 'EventJoin');
		$loaded = 0;

		foreach (glob($path . "*.php") as $event) {
			$eventClass = 'Event' . ucfirst(substr(basename($event), 0, -4));
			
			// register event listener
			$event = new $eventClass;
			$this->addListener($event);
			
			if (in_array($eventClass, $required)) {
				$loaded++;
			}
		}
		
		if ($loaded < count($required)) {
			die(
				"Failed to load required events: " .
				$loaded . "/" . count($required) . " loaded.\n"
			);
		}
	
	}
	
	protected function _onTick($response) {
	
		foreach ($this->_listeners as $event) {
			if ($event->respondsTo($response)) {
				$event->run($this->_client);
			}
		}
	
	}
	
	public function run() {
	
		$this->_client->connect($this->_cfg);
	
		while ($this->_client->connected()) {
			$response = $this->_client->readLine();
			echo $response; // debug			
			$this->_onTick($response);
		}
	
	}

}