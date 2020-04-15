<?php

$conf = parse_ini_file("config/conf.ini",true);


define('DB_TYPE'  ,$conf['datastore']['dtype']);

define('DB_HOST'  ,$conf['datastore']['dhost']);
define('DB_USER'  ,$conf['datastore']['dbuser']);
define('DB_PASS'  ,$conf['datastore']['dbpass']);
define('DB_NAME'  ,$conf['datastore']['dbname']);



define('MTN_REQUEST_URL' ,$conf['operator']['url']);
define('MTN_USER' ,$conf['operator']['username']);
define('MTN_PASS' ,$conf['operator']['password']);

define('GW_REQUEST_URL' ,$conf['gwparams']['url']);
