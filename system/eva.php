<?php

/**
 * Eva extends the drone and is a facade to common IRC commands
 */
 
class Eva extends Drone {
	
	public function getNick($lowercase = false) {
		$nick = trim($this->cfg['bot.nick']);
		return ($lowercase ? strtolower($nick) : $nick);
	}

	public function getPassword() {
		$pswd = false;
		
		if (!empty($this->cfg['bot.admin.pswd'])) {
			$pswd = $this->cfg['bot.admin.pswd'];
		}
	
		return $pswd;
	}
	
	public function isAdmin($nick) {
		return in_array($nick, $this->cfg['bot.admins']);
	}
	
	public function say($msg, $target) {
		if (!empty($this->cfg['bot.textcolor'])) {
			$c = $this->cfg['bot.textcolor'];
			$msg = "\x03" . $c . $msg . "\x03";
		}
	
		$this->client->say($msg, $target);
	}

}