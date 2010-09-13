<?php

// Load other event listeners with this event

class Event_Loader extends Event_Base {

	protected $_nickname;
	protected $_action;
	protected $_event;

	public function __construct($bot) {
	
		if (empty($bot->cfg['botpasswd'])) {
			$e = new EventLoadException(
				__CLASS__ . ": Required password not specified in " .
				"\$config['botpasswd']. Please add it and restart the bot."
			);
			throw $e;
		}
		
		parent :: __construct($bot);
	
	}

	public function respondsTo($response) {
	
		$result = preg_match(
			"/:([^!]+)!.* PRIVMSG (\w+) :!admin (\w+) (\w+) (\w+)/",
			$response,
			$matches
		);
		
		if (!$result) {
			return $result;
		}
		
		list(, $this->_nickname, $target, $this->_action,
				$this->_event, $passwd) = $matches;
		
		if ($target == $this->_bot->cfg['nickname'] &&
			$passwd == $this->_bot->cfg['botpasswd']) {
			$result = true;
		}
		
		return $result;
	
	}
	
	public function run() {
	
		switch ($this->_action) {
			case 'load':
				if ($this->_bot->isListenerLoaded($this->_event)) {
					$msg = "Module already loaded: " . $this->_event;
				} else {
					if ($this->_bot->addListener($this->_event)) {
						$msg = "Loaded module: " . $this->_event;
					} else {
						$msg = "Failed to load module: " . $this->_event;
					}
				}
				break;
			case 'unload':
				if ($this->_bot->isListenerLoaded($this->_event)) {
					if ($this->_bot->removeListener($this->_event)) {
						$msg = "Unloaded module: " . $this->_event;
					} else {
						$msg = "Unable to unload module: " . $this->_event;
					}
				} else {
					$msg = "Cannot unload module, not loaded: " . $this->_event;
				}
				break;
			default:
				$msg = "command '{$this->_action}' unrecognised";
		}
	
		$this->_bot->client->say($msg, $this->_nickname);
	
	}

}