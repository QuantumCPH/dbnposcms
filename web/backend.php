<?php
if(($_SERVER['HTTP_HOST']=="m-easypay.com" || $_SERVER['HTTP_HOST']=="www.m-easypay.com") && ($_SERVER['REQUEST_URI']=="/backend.php"||$_SERVER['REQUEST_URI']=="/backend.php/") ){

Header( "HTTP/1.1 301 Moved Permanently" );
Header( "Location: http://admin.m-easypay.com" );
die("baran");
exit;
}
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
