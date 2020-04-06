<?php

require('lib/timezone-util.php');
#to use this class:
#edit the config.php file in the lib/ folder
#if you are in development, set the PRODUCTION value to false
#if you are in development, set your default timezone manually. This is because your timezone is not going to change in your development environment
#to display errors, set it to true. Remember to disable errors when in production environment


$class = new timezone_util();
$timezoneFromDBOrAnyWhere = date('Y-m-d H:i:s');
#$formatWeWant = 'd F Y';
#the class gets the timezone of the current user dynamically. Although we can still pass a timezone should we need to
#$timezoneWeWant = '';

echo $class->getDateTimeByTimeZone($timezoneFromDBOrAnyWhere);
