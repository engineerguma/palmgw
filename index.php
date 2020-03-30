<?php

/**************************************
* MVend Njiwa Payment Server.
Version 2.0.1
Deployed 1st April 2015.
**************************************/
ini_set('display_errors',1);
//ini_set('error_reporting', E_ALL);
require 'config.php';
//require 'library/settings.php';

function __autoload($class) {
    require LIBS . $class . ".php";
}

$app = new Bootstrap();
?>
