<?php

/**
 * Eva extends the drone and is a facade to common IRC commands
 */
 
class Eva extends Drone {

	public function getNick() {
	
		return $this->cfg['nickname'];
	
	}
	
	public function action($action, $channel) {
	
		$this->client->action($action, $channel);
	
	}

}