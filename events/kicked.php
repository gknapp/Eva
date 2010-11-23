<?php

// Respond to being kicked from a channel
// auto-rejoin's channel after 3 seconds

class Event_Kicked extends Event_Base {

	protected $_active = false;
	protected $_kicker;
	protected $_kickTime;
	protected $_channel; // channel to rejoin
	protected $_rejoin;

	public function __construct($autoRejoin = 3) {
	
		$this->_rejoin = $autoRejoin;
	
	}

	public function respondsTo($response) {
	
		if (preg_match('/:([^!]+)!.+@([^\s+]) KICK ([^\s+]) ([^\s+])/', $response, $matches)) {
			list(, $kicker, $kickerHost, $channel, $user) = $matches;
			
			if ($user == $this->bot->cfg['bot.nick']) {
				$this->_active = true;
				$this->_kicker = $kicker;
				$this->_kickTime = time();
				$this->_channel = $channel;
			}
		}
	
		return $this->_active;
	
	}
	
	public function run() {
	
		if (time() > $this->_kickTime + $this->_rejoin) {
			$this->_bot->client->join($this->_channel);
			$this->_active = false;
		}
	
	}

}