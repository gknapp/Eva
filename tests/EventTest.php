<?php

require_once 'MockConnection.php';
require_once '../system/drone.php';
require_once '../system/eva.php';
require_once '../system/ircclient.php';
require_once '../system/receiver.php';
require_once '../events/base.php';

class EventTest extends PHPUnit_Framework_TestCase {

	protected $listener;
	protected $connection;

	public function setUp() {
		$this->connection = new MockConnection;
		$client = new IrcClient($this->connection);
		$eva = new Eva('../test.cfg', $client);
		$this->assignListener($eva);
	}

}