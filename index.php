<?php

require_once __DIR__ . '/vendor/autoload.php';


use App\DateCalculator;
use \DateTime;

$dc = new DateCalculator;
$date = new DateTime();
$date->setTimezone(new DateTimeZone('Europe/Budapest'));
$turnaround = 7;
echo $date->format('Y-m-d H:i').'<br>';
echo 'Turnaround: '.$turnaround.' hour(s)<br>';
echo $dc->calculateDueDate($date,$turnaround);
