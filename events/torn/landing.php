<?php

// alert someone when they're landing
// "You are currently on the plane flying to Torn City. \
// You should be there in 106 minutes!"

class Event_Torn_Landing extends Event_Base {

	public function respondsTo($event) {
		
		return $this->bot->receives($event)->inChannel()->match(
			".* plane flying to ([^\.]+).+ in (\d+) minutes"
		);
	
	}
	
	public function run($event) {
	
		list(, $destination, $mins) = $event->matches;
		
		$unit = ($mins == 1) ? 'minute' : 'minutes';
	
		$this->bot->say(
			"{$event->nick} I'll notify you when you land in " .
			"$destination in $mins $unit",
			$event->target
		);
		
		// calculate reminder time
		$time = date('H:i', time() + ($mins * 60));
		$msg = "{$event->nick} you are landing in $destination";
		
		$this->bot->at($time)->say($msg, $event->target);
		$this->bot->at($time)->privateMessage($msg, $event->nick);
	
	}
	
}