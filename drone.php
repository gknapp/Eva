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

	public function __construct(ircClient $client, $config) {
	
		$this->client = $client;
		$this->cfg = $config;
		
	}
	
	/**
	 * Prep the receiver class with the input line
	 * return instance of receiver
	 */
	public function receives($event) {
	
		return new Receiver($this, $event);
	
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
			$target = empty($e->target) ? $this->cfg['botadmin'] : $e->target;
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

	public static function getClassFromPathname($pathname) {
	
		$pathname = substr($pathname, strlen(dirname($pathname))+1, -4);
		
		// strip load ordering from symlink filename
		// e.g. 1-pong => pong
		if (preg_match("/^(\d+-)?(.+)/", $pathname, $match)) {
			$pathname = $match[2];
		}
		
		$class = join('_', array_map(function ($elem) {
				return ucfirst($elem);
			}, explode(DIRECTORY_SEPARATOR, $pathname))
		);
	
		return 'Event_' . $class;
	
	}
	
	public function run($eventDir = 'events-enabled/') {
	
		$this->client->connect($this->cfg);
		$this->_loadEvents($eventDir);
	
		while ($this->client->connected()) {
			$response = $this->client->readLine();
			echo $response; // debug
			
			foreach ($this->_listeners as $event) {
				if ($event->respondsTo($response)) {
					$event->run();
				}
			}
		}
	
	}
	
	/**
	 * Events should be polled no tick (timed events)
	 */
	public function watch(Event_Interval $event) {
	
		$this->_scheduled[] = $event;
	
	}

	protected function _loadEvents($path) {

		$required = array('Event_Pong', 'Event_Join');
		$loaded = 0;

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path),
			RecursiveIteratorIterator :: SELF_FIRST
		);
		
		foreach ($this->_orderPaths($files) as $pathName) {
			$event = self :: getClassFromPathname($pathName);
			$this->addListener($event);
			
			if (in_array($event, $required)) {
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

	/**
	 * order by filenames numerically to respect
	 * load order of symlinked events
	 */
	protected function _orderPaths($files) {
	
		$events = array();
		$pattern = '|' . DIRECTORY_SEPARATOR . '(\d+-)?.+|';
		
		foreach ($files as $file) {
			$filePath = $file->getPathName();
			
			if (preg_match($pattern, $filePath, $match)) {
				if (!empty($match[1])) {
					$events[intval($match[1])] = $filePath;
				} else {
					$events[] = $filePath;
				}
			}
		}
		
		ksort($events, SORT_NUMERIC);
		return $events;
	
	}
	
}