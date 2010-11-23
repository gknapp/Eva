<?php

// Load other event listeners with this event

class Event_Loader extends Event_Base {

	protected $_nickname;
	protected $_action;
	protected $_event;

	public function __construct($bot) {
	
		if (!$bot->getPassword()) {
			$e = new EventLoadException(
				__CLASS__ . ": Bot password not specified in config." .
				"Please set 'bot.admin.pswd' and restart eva."
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
		
		// if this msg is PM to the bot and password correct ...
		if ($target == $this->_bot->getNick() &&
			$passwd == $this->_bot->getPassword()) {
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