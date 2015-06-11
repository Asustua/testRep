<?php

define('ROOT', '/var/www/vhosts/sgupdate.com/httpdocs/test/ryd/mcv/');
define('SYSTEM', ROOT.'system/');
define('SITE', ROOT.'site/');
define('CONTROLLER', SITE.'controller/');
define('MODEL', SITE.'model/');


define('SYS_EXTENSION', '.php');

define('DB_USER', 'test');
define('DB_HOST', 'localhost');
define('DB_PASSWORD', 'test');
define('DB_DATABASE', 'test');
include_once SYSTEM.'action.php';
include_once SYSTEM.'controller.php';
include_once SYSTEM.'handler.php';
include_once SYSTEM.'database.php';
include_once SYSTEM.'model.php';

?>
