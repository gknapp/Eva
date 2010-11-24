<?php

class EventLoadException extends Exception {
	public $target; // nickname to direct error msg
}

class Drone {

	public $client;
	public $storage;
	public $cfg;
	public $log;
	
	protected $_listeners = array();
	protected $_scheduled = array();

	public function __construct($config) {
	
		$this->client = new IrcClient;
		$this->storage = new FileStore('memory/');
		$this->cfg = $this->_parseConfig($config);
		
	}
	
	protected function _parseConfig($file) {
	
		$data = file($file);
		$config = array('events' => array());
		
		foreach ($data as $line) {
			// read config settings
			if (preg_match("/^([a-z\.]+)\s?=\s?([#a-z0-9\.\-,+\!]+)/i", $line, $match)) {
				list(, $option, $value) = $match;
				$option = strtolower($option);
				
				// explode comma separated values
				if (strpos($value, ',') !== false) {
					$value = explode(',', $value);
				}
				
				// server.channels is expected to be an array
				if ($option == 'server.channels' && !is_array($value)) {
					$value = array($value);
				}
				
				$config[$option] = $value;
			}
			
			// read events to load
			if (preg_match("/^Event_[a-z0-9_]+/i", $line, $match)) {
				$config['events'][] = trim(current($match));
			}
		}
		
		return $config;
	
	}
	
	/**
	 * Prep the receiver class with the input line
	 * return instance of receiver
	 */
	public function receives($event) {
	
		return new Receiver($this, $event);
	
	}
	
	/**
	 * Issue a response at a given time
	 */
	public function at($time) {
	
		return $this->_scheduleAction($time);
	
	}
	
	public function isListenerLoaded($listener) {
	
		foreach ($this->_listeners as $event) {
			if ($listener instanceof $event) {
				return true;
			}
		}
		
		return false;
	
	}
	
	public function addListener($listener) {
	
		$result = false;
	
		try {
			if (!$this->isListenerLoaded($listener)) {
				$this->_listeners[] = new $listener($this);
				$result = true;
			}
		} catch (EventLoadException $e) {
			$target = empty($e->target) ? $this->cfg['bot.admin'] : $e->target;
			$this->client->say($e->getMessage(), $target);
			// $this->log->append($e->getMessage() . " ({$e->target})");
		}
		
		return $result;
	
	}
	
	public function removeListener($listener) {
	
		foreach ($this->_listeners as $i => $event) {
			if (strtolower(get_class($event)) == strtolower($listener)) {
				unset($this->_listeners[$i]);
				return true;
			}
		}
		
		return false;
	
	}
	
	public function run() {
	
		$this->client->connect($this->cfg);
		$this->_loadEvents();
	
		while ($this->client->connected()) {
			$input = $this->client->readLine();
			echo $input; // debug
			
			// run events that respond to this input
			foreach ($this->_listeners as $event) {
				if ($event->respondsTo($input)) {
					$event->run();
				}
			}
			
			// run scheduled actions
			foreach ($this->_scheduled as $i => $action) {
				if ($action->runNow()) {
					$action->run($this);
					$this->_removeAction($i);
				}
			}
		}
	
	}
	
	public function getNick() {
	
		return $this->cfg['bot.nick'];
	
	}

	public function getPassword() {
	
		$pswd = false;
		
		if (!empty($this->cfg['bot.admin.pswd'])) {
			$pswd = $this->cfg['bot.admin.pswd'];
		}
	
		return $pswd;
	
	}
	
	protected function _scheduleAction($time) {
	
		$action = new ScheduledAction($time);
		$this->_scheduled[] = $action;
		
		return $action;
	
	}
	
	protected function _removeAction($idx) {
	
		unset($this->_scheduled[$idx]);
	
	}
	
	protected function _loadEvents() {

		// Always load these required events
		$required = array('Event_Pong', 'Event_Join');
		$events = array_merge($required, $this->cfg['events']);
		$loaded = 0;
		
		foreach ($events as $event) {
			$this->addListener($event);
			
			if (in_array($event, $required)) {
				$loaded++;
			}
		}
		
		if ($loaded < count($required)) {
			die("Failed to load required events: " .
				$loaded . "/" . count($required) . " loaded.\n");
		}
	
	}
	
}

class ScheduledAction {

	protected $_time;
	protected $_call;

	public function __construct($time) {
	
		$this->_time = $time;
	
	}
	
	public function runNow() {
	
		$result = false;
	
		if ($this->_time == date('H:i')) {
			$result = true;
		}
		
		return $result;
	
	}
	
	public function say($msg, $target) {
	
		$this->_call[] = array(
			'name' => 'say',
			'args' => array($msg, $target)
		);
	
	}
	
	public function privateMessage($msg, $target) {
	
		$this->_call[] = array(
			'name' => 'privateMessage',
			'args' => array($msg, $target)
		);	
	
	}
	
	public function run($bot) {
	
		$ref = new ReflectionClass(get_class($bot));
		
		foreach ($this->_call as $meth) {
			$method = $ref->getMethod($meth['name']);
			$method->invokeArgs($bot, $meth['args']);
		}
	
	}
	
}