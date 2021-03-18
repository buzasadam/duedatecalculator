<?php

require_once __DIR__ . '/vendor/autoload.php';


use App\DateCalculator;
use Other\Log;
//use \DateTime;

$log = new Log;
$log->m_log('Use log outside');
$dc = new DateCalculator($log);
$date = new DateTime();
$date->setTimezone(new DateTimeZone('Europe/Budapest'));
$turnaround = 8;
echo $date->format('Y-m-d H:i').'<br>';
echo 'Turnaround: '.$turnaround.' hour(s)<br>';
echo $dc->calculateDueDate($date,$turnaround);
