<?php

abstract class EventBase {

	abstract public function respondsTo($response);
	abstract public function run($client, $config);

}