<?php

require_once 'EventTest.php';
require_once '../events/identify.php';

class EventIdentifyTest extends EventTest {

	public function assignListener($eva) {
		$this->listener = new Event_Identify($eva);
	}

	public function testRespondToThisNickIsRegistered() {
		$event = $this->listener->respondsTo(
			':NickServ!services@server.com NOTICE Eva ' .
			':This nickname is registered and protected.'
		);
		$this->assertInstanceOf('Event', $event);
		
		$this->listener->run($event);
		$this->assertEquals(
			"PRIVMSG NickServ :IDENTIFY nickpass",
			$this->connection->readResponse()
		);
	}
	
	public function testOnlyRespondToNickService() {
		$event = $this->listener->respondsTo(
			':Attacker!services@torn.com NOTICE Eva ' .
			':This nickname is registered and protected.'
		);
		$this->assertEquals(false, $event);
	}

}