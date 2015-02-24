<?php
ini_set('memory_limit', '-1');

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('b2c', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
