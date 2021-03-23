<?php

require_once __DIR__ . '/vendor/autoload.php';


use App\DateCalculator;
use Other\Log;
use Container\DIContainer;

// A container regisztrálja a függőségeket __contruct alapján
$container = new DIContainer;

// App\DateCalculator hozzáadása a containerher
// ezért nem kell előtte Log egyedet létrehozni
$container->set('App\DateCalculator');

// App\DateCalculator egyed létrehozása konténerből
$dc = $container->get('App\DateCalculator');

// Jelenlegi időpont és időzóna beállítás
$date = new DateTime();
$date->setTimezone(new DateTimeZone('Europe/Budapest'));

// Válaszadási határidő
$turnaround = 5;

// Eredmény
echo $date->format('Y-m-d H:i').'<br>';
echo 'Turnaround: '.$turnaround.' hour(s)<br>';
echo $dc->calculateDueDate($date,$turnaround);
