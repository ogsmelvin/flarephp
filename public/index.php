<?php

//$time = microtime();
//$time = explode(' ', $time);
//$time = $time[1] + $time[0];
//$start = $time;

$appDirectory = dirname(dirname(__FILE__));

require $appDirectory.'/system/flare.php';
use Flare\Flare as F;

F::createApp()->setAppDirectory($appDirectory.'/application')->start();

//$time = microtime();
//$time = explode(' ', $time);
//$time = $time[1] + $time[0];
//$finish = $time;
//$total_time = round(($finish - $start), 4);
//echo '<div class="alert alert-success">Page generated in '.$total_time.' seconds.</div>';