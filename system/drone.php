<?php

class EventLoadException extends Exception {
	public $target; // nickname to direct error msg
}

class Drone {

	public $client;
	public $cfg;
	
	protected $_storage;	
	protected $_listeners = array();
	protected $_scheduled = array();
	protected $_channels = array(); // channels the bot is on

	public function __construct($config, $client) {
		$this->client = $client;
		$this->cfg = $this->_parseConfig($config);
		//$this->_storage = new FileStore('memory/');
	}
		
	/**
	 * Prep the receiver class with the input line
	 * return instance of receiver
	 */
	public function receives($event) {
		return new Receiver($this, $event);
	}

	public function privateMessage($msg, $target) {
		$this->client->say($msg, $target);
	}
	
	public function action($action, $channel) {
		$this->client->action($action, $channel);
	}
	
	public function join($channel) {
		if (!$this->onChannel($channel)) {
			$this->client->join($channel);
			$this->addChannel($channel);
		}
	}
	
	public function part($channel) {
		if ($this->onChannel($channel)) {
			$this->client->part($channel);
			$this->removeChannel($channel);
		}
	}
	
	public function notice($msg, $target) {
		$this->client->notice($msg, $target);
	}
	
	public function quit($msg = 'kthxbye') {
		$this->client->quit($msg);
	}
	
	/*
	public function store($key = null, $value = null) {
		if (empty($key) && empty($value)) {
			return $this;
		}
	}
	
	public function asPublic($key, $value) {
		// store in shared file
	}*/
	
	public function at($time) {
		return $this->_scheduleAction($time);
	}
	
	public function getChannels() {
		return $this->_channels;
	}
	
	/**
	 * Append list of active channels
	 */
	protected function addChannel($channel) {
		$this->_channels[] = $channel;
	}
	
	/**
	 * Remove channel from list of active channels
	 */
	protected function removeChannel($channel) {
		$key = array_search($channel, $this->_channels);
		
		if ($key !== false) {
			unset($this->_channels[$key]);
		}
		
		return ($key !== false) ? true : false;
	}
	
	protected function onChannel($channel) {
		return in_array($channel, $this->_channels);
	}
	
	public function onMultipleChannels() {
		return (count($this->_channels) > 1);
	}
	
	protected function isListenerLoaded($listener) {
		foreach ($this->_listeners as $event) {
			if ($listener instanceof $event) {
				return true;
			}
		}
		
		return false;
	}
	
	protected function addListener($listener) {
		$result = false;
	
		try {
			if (!$this->isListenerLoaded($listener)) {
				$this->_listeners[] = new $listener($this);
				$result = true;
			}
		} catch (EventLoadException $e) {
			if (empty($e->target)) {
				list($target) = $this->cfg['bot.admins'];
			} else {
				$target = $e->target;
			}
			
			$this->client->say($e->getMessage(), $target);
		}
		
		return $result;
	}
	
	protected function removeListener($listener) {
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
				if (($e = $event->respondsTo($input))) {
					$event->run($e);
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
				
				// settings are expected to be an array
				if (in_array($option, array('server.channels','bot.admins'))) {
					$value = $this->_makeArray($value);
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
	
	protected function _makeArray($value) {
			
		return !is_array($value) ? array($value) : $value;
	
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
		$events = array_unique(array_merge($required, $this->cfg['events']));
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
	
	public function notice($msg, $target) {
	
		$this->_call[] = array(
			'name' => 'notice',
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