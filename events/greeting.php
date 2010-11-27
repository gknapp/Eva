<?php

// Say hello if spoken to ...

class Event_Greeting extends Event_Base {

	/**
	 * Do I respond to this event?
	 */
	public function respondsTo($event) {
	
		$event = $this->bot->receives($event);
		return $event->match("h[ielloyawd]+ (\w+)");
	
	}
	
	/**
	 * Perform action in response to event
	 */
	public function run($event) {
	
		$nickname = strtolower($event->matches[1]);
		
		if ($nickname == $this->bot->getNick(true)) {
			$this->bot->say("hi " . $event->nick, $event->target);
		}
	
	}

}