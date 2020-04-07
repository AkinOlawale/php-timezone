<?php

session_start();

define('PRODUCTION', false); #are we in the production environment? If we are this class will get the timezone dynamically
define('DEFAULT_TIMEZONE', 'Africa/Johannesburg'); #Tell the class the default timezone to use in development. In production this class uses UTC as default
define('DISPLAY_ERRORS', true); #display errors?
