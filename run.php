<?php

require("connection.php");
require("client.php");
require("bot.php");

require("events/base.php");
require("events/pong.php");
require("events/identify.php");
require("events/join.php");
require("events/slap.php");

require("config.php");

// so the bot don't stop
set_time_limit(0);

$bot = new ircBot(new ircClient);
$bot->addListener(new EventIdentify);
$bot->addListener(new EventJoin);
$bot->addListener(new EventSlap);
$bot->run($config);
