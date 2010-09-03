<?php

// so the bot don't stop
set_time_limit(0);

function __autoload($className) {

	$file = strtolower($className);

	if (substr($className, 0, 5) == 'Event') {
		$file = 'events-available/' . substr(strtolower($className), 5);
	} else if (substr($className, 0, 3) == 'irc') {
		$file = substr(strtolower($className), 3);
	}
	
	$file .= '.php';
	
	if (file_exists($file)) {
		require($file);
	} else {
		trigger_error(
			"Cannot load class '$className', expected class file " . 
			"does not exist: $file"
		);
	}

}

require("config.php");

$eva = new ircBot(new ircClient, $config);
$eva->loadEvents('events-enabled/');
$eva->run();
