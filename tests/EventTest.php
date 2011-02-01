<?php

require_once 'MockConnection.php';
require_once '../system/drone.php';
require_once '../system/eva.php';
require_once '../system/ircclient.php';
require_once '../system/receiver.php';
require_once '../events/base.php';

class EventTest extends PHPUnit_Extensions_OutputTestCase {

	protected $listener;

	public function setUp() {
		$client = new IrcClient(new MockConnection);
		$eva = new Eva('../test.cfg', $client);
		$this->assignListener($eva);
	}

}