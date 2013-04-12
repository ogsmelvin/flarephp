<?php

$appDirectory = dirname(dirname(__FILE__));

require $appDirectory.'/system/adk.php';
use ADK\Adk as A;

A::mvc()->setAppDirectory($appDirectory.'/application')->start();
