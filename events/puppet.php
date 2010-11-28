<?php

// Admin users can respond / speak as the bot
// "!say [#channel] hello world"

class Event_Puppet extends Event_Base {

	public function respondsTo($event) {
		
		$event = $this->bot->receives($event)->asPrivateMessage()->fromAdmin();
		return $event->match("!(say|action)\s+(#[a-z0-9-]+\s)?(.+)");
	
	}
	
	public function run($event) {
	
		list(, $cmd, $channel, $msg) = $event->matches;
			
		if (empty($channel) && $this->bot->onMultipleChannels()) {
			$this->bot->privateMessage(
				"I am on more than one channel, please specify a channel " .
				"for your msg: !say #channel message",
				$event->nick
			);
			
			return false;
		} elseif (empty($channel)) {
			$channel = current($this->bot->getChannels());
		}
		
		if ($this->bot->onChannel($channel)) {
			if ($cmd == 'say') {
				$this->bot->say($msg, $channel);
			} else {
				$this->bot->action($msg, $channel);
			}
		} else {
			$this->bot->privateMessage("I'm not in $channel", $event->nick);
		}
	
	}
	
}