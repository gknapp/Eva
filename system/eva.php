<?php

/**
 * Eva extends the drone and is a facade to common IRC commands
 */
 
class Eva extends Drone {

	public function getNick() {
	
		return $this->cfg['nickname'];
	
	}
	
	public function say($msg, $target) {
	
		if (!empty($this->cfg['textcolor'])) {
			$c = $this->cfg['textcolor'];
			$msg = "\x03" . $c . $msg . "\x03";
		}
	
		$this->client->say($msg, $target);
	
	}
	
	public function privateMessage($msg, $target) {
	
		$this->client->say($msg, $target);
	
	}
	
	public function action($action, $channel) {
	
		$this->client->action($action, $channel);
	
	}
	
	public function join($channel) {
	
		$this->client->join($channel);
	
	}
	
	public function part($channel) {
	
		$this->client->part($channel);
	
	}
	
	public function notice($msg, $target) {
	
		$this->client->notice($msg, $target);
	
	}
	
	public function quit($msg = 'kthxbye') {
	
		$this->client->quit($msg);
	
	}

}