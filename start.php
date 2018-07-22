<?php

require 'vendor/autoload.php';

$server = new \Radius\radius(__DIR__);
$server->run();
