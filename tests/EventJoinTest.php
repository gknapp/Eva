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
		$this->expectOutputString("JOIN #testing");
		$this->listener->run($event);
	}
	
	public function testJoinChannelOnAdminPrivateMessage() {
		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG eva :!join #test'
		);
		$this->assertInstanceOf('UserEvent', $event);
		
		$this->expectOutputString("JOIN #test");
		$this->listener->run($event);
	}
	
	public function testJoinChannelOnAdminChannelCommand() {
		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG #test :!join #test'
		);
		$this->assertInstanceOf('UserEvent', $event);
		
		$this->expectOutputString("JOIN #test");
		$this->listener->run($event);
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
		$this->expectOutputString("JOIN #testing");
		$this->listener->run($event);

		$event = $this->listener->respondsTo(
			':Greg!Greg@host.com PRIVMSG #testing :!join #testing'
		);
		$this->expectOutputString("");
		$this->listener->run($event);
	}

}