<?php

require_once 'EventTest.php';
require_once '../events/pong.php';

class EventPongTest extends EventTest {

	public function assignListener($eva) {
		$this->listener = new Event_Pong($eva);
	}

	public function testRespondsToPing() {
		$event = $this->listener->respondsTo(
			'PING :irc.server.com'
		);
		$this->assertInstanceOf('Event', $event);
		
		$this->expectOutputString("PONG :irc.server.com");
		$this->listener->run($event);
	}

}