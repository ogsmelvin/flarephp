<?php

$appDirectory = dirname(dirname(__FILE__));

require $appDirectory.'/system/fphp.php';
use FPHP\Fphp as F;

F::createApp()->setAppDirectory($appDirectory.'/application')->start();