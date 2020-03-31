<?php

$conf = parse_ini_file("config/conf.ini",true);


define('MTN_REQUEST_URL'  ,$conf['operator']['url']);
define('MTN_USER'  ,$conf['operator']['username']);
define('MTN_PASS'  ,$conf['operator']['password']);

define('GW_REQUEST_URL'  ,$conf['operator']['url']);
