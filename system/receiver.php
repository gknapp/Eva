<?php

/**
 * Utility class to provide friendly interface to respond to
 * common IRC events.
 *
 * @package eva
 */
class Receiver {

	protected $_bot;
	protected $_input;
	protected $_channel;

	public function __construct($bot, $input) {
	
		$this->_bot = $bot;
		$this->_input = $input;
	
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
		
		$regex = array(
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
		
		// must receive the message in a channel?
		if (!empty($this->_channel)) {
			$regex['command'] = '(PRIVMSG)';
			
			if ($this->_channel == '*') { // any channel	
				$regex['target'] = '(#[^\s]+)';
			} else { // specific channel
				$regex['target'] = '(' . $this->_channel . ')';
			}
		}
		
		$pattern = ':' . join('', $regex) . ' :' . $pattern;
		$pattern = $insensitive ? "/$pattern/i" : "/$pattern/";
		echo $pattern . "\n";
		
		if (preg_match($pattern, $this->_input, $matches)) {
			$event = new Event($matches);
		}
		
		return $event;
	
	}

	/**
	 * Respond to private message event with a specific text pattern
	 * @param regex $pattern string
	 */
	public function asPrivateMessage($pattern) {
	
		$result = $this->match($pattern);
		
		if ($event) { // is this is a private message to the bot?
			$result = $event->isPrivateMessage($this->bot->getNick());
		}
		
		return $result;
	
	}
	
	/**
	 * To be implemented
	 */
	public function asNotice() {
	
	}
	
	/**
	 * Filter out events not received in a channel, by default accept events 
	 * on any channel unless a channel is specified
	 */
	public function inChannel($channel = '') {
		
		$this->_channel = '*';
		
		if (!empty($channel)) {
			$this->_channel = $channel;	
		}
		
		return $this;
		
	}

}

/**
 * Encapsulate event response
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
class Event {

	public $occured;
	public $nick;
	public $user;
	public $host;
	public $command;
	public $target;
	public $matches;

	public function __construct($matches) {
	
		$this->occured = time();
		$this->matches = array(array_shift($matches));
		
		list($this->nick, $this->user, $this->host,
			 $this->command, $this->target) = $matches;
		
		// stuff any remaining captures into matches attribute
		if (count($matches) > 5) {
			$matches = array_slice($matches, 5);
			
			while (count($matches)) {
				$this->matches[] = array_shift($matches);
			}
		}
	
	}
	
	/**
	 * Check if event is a private message
	 * @param $to nickname (optional)
	 */
	public function isPrivateMessage($to = '') {
	
		$result = ($this->command === 'PRIVMSG');
		
		if ($result && !empty($to)) {
			$result = ($this->target === $to);
		}
		
		return $result;
	
	}

}
