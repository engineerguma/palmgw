<?php


ini_set('display_errors',1);
//ini_set('error_reporting', E_ALL);
require 'config.php';
require 'library/settings.php';

spl_autoload_register(function($class) {
     require LIBS . $class . ".php";
});

$app = new Bootstrap();
?>
