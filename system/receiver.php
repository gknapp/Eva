<?php

/**
 * Utility class to provide friendly interface 
 * to filter a response to common IRC events.
 *
 * @package eva
 */
class Receiver {

	protected $_bot;
	protected $_input;
	protected $_regex;

	public function __construct($bot, $input) {
	
		$this->_bot = $bot;
		$this->_input = $input;
		
		$this->_regex = array(
			'nick'   =>'([^!]+)',
			'!',
			'user'   =>'([^@]+)',
			'@',
			'host'   =>'([^\s]+)',
			' ',
			'command'=>'([A-Z]+)',
			' ',
			'target' =>'([^\s]+)',
		);
	
	}

	/**
	 * Reset regex to match whatever is specified
	 * for system events. eg. responding to PING?
	 */
	public function raw($pattern, $insensitive = true) {
		
		$event = false;
		$pattern = $insensitive ? "/$pattern/i" : "/$pattern/";
		
		if (preg_match($pattern, $this->_input, $matches)) {
			$event = new SystemEvent($matches);
		}
		
		return $event;
	
	}

	/**
	 * Filters events to those from $nick
	 */
	public function from($nick) {
		
		$this->_regex['nick'] = '(' . $nick . ')';
		return $this;
		
	}
	
	/**
	 * Filter events to those from admin users
	 */
	public function fromAdmin() {
	
		return $this->from(join('|', $this->_bot->cfg['bot.admins']));
	
	}

	/**
	 * Filters events to those from $hostname
	 */	
	public function fromHost($hostname) {
	
		$this->_regex['host'] = '(' . $hostname . ')';
		return $this;
	
	}

	/**
	 * Match a response regardless of origin
	 *
	 * Example input:
	 *   :Joe!Bloggs@cable.virginmedia.com PRIVMSG #lobby :!slap Fred
	 *
	 * @param $pattern regex string
	 */
	public function match($pattern, $insensitive = true) {
	
		$event = false;
		$pattern = ':' . join('', $this->_regex) . ' :' . $pattern;
		$pattern = $insensitive ? "/$pattern/i" : "/$pattern/";
		
		if (preg_match($pattern, $this->_input, $matches)) {
			$event = new Event($matches);
		}
		
		return $event;
	
	}

	/**
	 * Filter out events not received in a channel, by default accept events 
	 * on any channel unless a channel is specified
	 */
	public function inChannel($channel = '*') {
		
		$this->_regex['command'] = '(PRIVMSG)';
			
		if (empty($channel) || $channel == '*') { // any channel	
			$this->_regex['target'] = '(#[^\s]+)';
		} else { // specific channel
			$this->_regex['target'] = '(' . $channel . ')';
		}
		
		return $this;
		
	}

	/**
	 * Filter out events that are not private messages
	 */
	public function asPrivateMessage() {
	
		$this->_regex['command'] = '(PRIVMSG)';
		$this->_regex['target'] = '(' . $this->_bot->getNick() . ')';
		return $this;
	
	}
	
	/**
	 * Filter events that are not notices
	 */
	public function asNotice() {
		
		$this->_regex['command'] = '(NOTICE)';
		return $this;
		
	}

}

/**
 * Encapsulate raw event
 * Used for system events, no pattern template
 */
class SystemEvent {

	public $occured;
	public $matches = array();

	public function __construct($matches) {
	
		$this->occured = time();
		
		foreach ($matches as $match) {
			$this->matches[] = trim($match);
		}
	
	}

}


/**
 * Encapsulate event
 *
 * Example:
 *   :Joe!Bloggs@cable.virginmedia.com PRIVMSG #lobby :!slap Fred
 *   
 *   $nick    = Joe
 *   $user    = Bloggs
 *   $host    = cable.virginmedia.com
 *   $command = PRIVMSG
 *   $target  = #lobby
 *   $matches = !slap Fred [see match()]
 */
class Event extends SystemEvent {

	public $nick;
	public $user;
	public $host;
	public $command;
	public $target;

	public function __construct($matches) {
	
		$this->occured = time();
		$this->matches = array(array_shift($matches));
		
		list($this->nick, $this->user, $this->host,
			 $this->command, $this->target) = $matches;
		
		// stuff any remaining captures into matches attribute
		if (count($matches) > 5) {
			foreach (array_slice($matches, 5) as $match) {
				$this->matches[] = trim($match);
			}
		}
	
	}

}