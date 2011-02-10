<?php

// alert someone when their energy bar is full
// "Energy: 145/150"

class Event_Torn_Energy extends Event_Base {

	public function respondsTo($event) {
		
		return $this->bot->receives($event)->inChannel()->match(
			"Energy:\s(\d+)\/(\d+)"
		);
	
	}
	
	public function run($event) {
	
		list(, $energy, $total) = $event->matches;
		
		if ($energy < $total) {
			$remaining = $this->_calcRemaining($energy, $total);
		
			// work out time
			$hr = floor($remaining / 60);
			$min = round(($remaining / 60 - $hr) * 60);
		
			$duration = $this->_timeToText($hr, $min);
			$time = date('H:i', strtotime("+$remaining minutes"));
	
			$msg = $event->nick . " your energy will be full in " .
				$duration . " ($time)";
				
			$this->_setReminder($time, $event);
		} else if ($energy == $total) {
			$msg = $event->nick . " your energy is full";
		} else {
			$msg = $event->nick . " whoa ... you are high dude!";
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
	
	protected function _calcRemaining($energy, $total) {
	
		$min = date('i');
		
		// calc minutes until next tick
		// donator = 10 min, otherwise 15 min
		$tickTime = ($total == 150) ? 10 : 15;
		$nextTick = (ceil($min / $tickTime) * $tickTime) - $min;
		$intervals = ($total - $energy) / 5 - 1;
		
		return ($tickTime * $intervals + $nextTick);
	
	}
	
	protected function _setReminder($time, $event) {
	
		$msg = "{$event->nick} your energy is full";
		$this->bot->at($time)->say($msg, $event->target);
		$this->bot->at($time)->privateMessage($msg, $event->nick);
	
	}
	
}