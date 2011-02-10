<?php

// Calculate number of shares to skim
// "!skim 7000000 @ $145.631"

class Event_Torn_Skim extends Event_Base {

	public function respondsTo($event) {
		
		return $this->bot->receives($event)->match(
			'!skim\s+([\d+,]+)\s+@\s+\$?([\d,\.]+)'
		);
	
	}
	
	public function run($event) {
	
		list(,$skim, $price) = $event->matches;
		$skim = str_replace(',','', $skim);
		$price = str_replace(',', '', $price);
		$shares = ceil($skim / $price);
		$skimAmount = number_format(round($shares * $price));
		
		$this->bot->say(
			$event->nick . " skim $shares shares for a block worth $" . $skimAmount,
			$event->target
		);
	
	}
		
}