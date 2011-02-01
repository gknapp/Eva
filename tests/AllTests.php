<?php

class AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Events');
		$suite->addTestFile('EventPongTest.php');
		$suite->addTestFile('EventJoinTest.php');
		return $suite;
	}

}