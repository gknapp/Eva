<?php

// Calculate when drug side effects wear off
// "!drug 112 mins"

class Event_Torn_Drug extends Event_Base {

	public function respondsTo($event) {
		
		return $this->bot->receives($event)->inChannel()->match(
			"!drug\s+(\d+)"
		);
	
	}
	
	public function run($event) {
	
		list(, $remain) = $event->matches;
		
		if ($remain > 0) {
			// work out time
			$hr = floor($remain / 60);
			$min = round(($remain / 60 - $hr) * 60);
		
			$duration = $this->_timeToText($hr, $min);
			$time = date('H:i', strtotime("+$remain minutes"));
	
			$msg = "{$event->nick} your side effects will wear off in " .
				   "$duration ($time)";
				
			$this->_setReminder(
				"your side effects have worn off", $time, $event
			);
		} elseif (empty($remain)) {
			$msg = "{$event->nick} your side effects have worn off";
		} else {
			$msg = "{$event->nick} you're still high, " . 
				   "and stop grinding your teeth";
		}
		
		$this->bot->say($msg, $event->target);
	
	}
	
	protected function _timeToText($hour, $min) {
	
		$duration = '';
		
		if ($hour) {
			$duration .= $hour . ($hour > 1 ? ' hrs' : ' hr');
		}
		
		if ($min) {
			$duration .= ' ' . $min . ($min > 1 ? ' mins' : ' min');
		}
		
		return trim($duration);
	
	}
		
	protected function _setReminder($msg, $time, $event) {
	
		$msg = $event->nick . " " . $msg;
		$this->bot->at($time)->say($msg, $event->target);
		$this->bot->at($time)->privateMessage($msg, $event->nick);
	
	}
	
}