<?php

// alert someone when their nerve bar is full
// "Nerve: 3/39"

class Event_Torn_Nerve extends Event_Base {

	public function respondsTo($event) {
		
		return $this->bot->receives($event)->inChannel()->match(
			"Nerve:\s(\d+)\/(\d+)"
		);
	
	}
	
	public function run($event) {
	
		list(, $nerve, $total) = $event->matches;
		
		if ($nerve < $total) {
			$remaining = $this->_calcRemaining($nerve, $total);
		
			// work out time
			$hr = floor($remaining / 60);
			$min = round(($remaining / 60 - $hr) * 60);
		
			$duration = $this->_timeToText($hr, $min);
			$time = date('H:i', strtotime("+$remaining minutes"));
	
			$msg = $event->nick . " your nerve bar will be full in " .
				$duration . " ($time)";
				
			$this->_setReminder($time, $event);
		} else if ($nerve == $total) {
			$msg = $event->nick . " your nerve bar is full";
		} else {
			$msg = $event->nick . " umm ... what? That can't be ...";
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
	
	protected function _calcRemaining($nerve, $total) {
	
		$min = date('i');
		$tickTime = 5; // 1 nerve regens every 5 minutes
		
		// calc minutes until next tick
		$nextTick = (ceil($min / $tickTime) * $tickTime) - $min;
		$intervals = $total - $nerve - 1;
		
		return ($tickTime * $intervals + $nextTick);
	
	}
	
	protected function _setReminder($time, $event) {
	
		$msg = "{$event->nick} your nerve bar is full";
		$this->bot->at($time)->say($msg, $event->target);
		$this->bot->at($time)->privateMessage($msg, $event->nick);
	
	}
	
}