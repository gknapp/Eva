<?php

require_once 'EventTest.php';
require_once '../events/join.php';

class EventJoinTest extends EventTest {

	public function assignListener($eva) {
		$this->listener = new Event_Join($eva);
	}

	public function testJoinChannelOnMessageOfTheDay() {
		$event = $this->listener->respondsTo(
			':irc.server.com 376 Eva :End of /MOTD command.'
		);
		$this->assertInstanceOf('Event', $event);
		$this->listener->run($event);
		$this->assertEquals("JOIN #testing", $this->connection->readResponse());
	}
	
	public function testJoinChannelOnAdminPrivateMessage() {
		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG Eva :!join #test'
		);
		$this->assertInstanceOf('UserEvent', $event);
		$this->listener->run($event);
		$this->assertEquals("JOIN #test", $this->connection->readResponse());
	}

	public function testJoinChannelOnAdminChannelCommand() {
		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG #test :!join #test2'
		);
		$this->assertInstanceOf('UserEvent', $event);		
		$this->listener->run($event);
		$this->assertEquals("JOIN #test2", $this->connection->readResponse());
	}
	
	public function testIgnoreJoinCommandFromUser() {
		$event = $this->listener->respondsTo(
			':User!user@host.com PRIVMSG #test :!join #test'
		);
		$this->assertFalse($event);
	}
	
	public function testDoNotJoinChannelTwice() {
		$event = $this->listener->respondsTo(
			':irc.server.com 376 Eva :End of /MOTD command.'
		);
		$this->listener->run($event);
		$this->assertEquals("JOIN #testing", $this->connection->readResponse());

		$this->connection->clearResponse();
		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG #test :!join #testing'
		);
		$this->listener->run($event);
		$this->assertEquals("", $this->connection->readResponse());
	}

}