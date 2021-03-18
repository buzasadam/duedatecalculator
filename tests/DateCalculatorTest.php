<?php

use PHPUnit\Framework\TestCase;
use App\DateCalculator;
use Other\Log;


final class DateCalculatorTest extends TestCase
{
    /**
     * testCalculation Tests when turnaround is 0 then due time is the submit time
     * @return void
     */
    public function testCalculationZeroTurnAround() :void
    {
        $log = new Log;
        $dt = new DateCalculator($log);
        $submittime = new DateTime();
        $submittime->setTimezone(new DateTimeZone('Europe/Budapest'));
        $turnaround = 0;
        $result = $dt->calculateDueDate($submittime, $turnaround);
        $this->assertStringContainsString($submittime->format('Y-m-d H:i'), $result);
    }


    /**
     * testCalculationSameDay Tests when due date is the same day - seconds don't matter
     * @return void
     */
    public function testCalculationSameDay() :void
    {
        $log = new Log;
        $dt = new DateCalculator($log);
        $format = 'Y-m-d H:i:s';
        $submittime = DateTime::createFromFormat($format, '2021-03-16 14:00:12');
        $turnaround = 2;
        $result = $dt->calculateDueDate($submittime, $turnaround);
        $this->assertStringContainsString('2021-03-16 16:00', $result);
     }


     /**
      * testCalculationNextDay Tests when due date is the next day - seconds don't matter
      * @return void
      */
     public function testCalculationNextDay() :void
     {
       $log = new Log;
       $dt = new DateCalculator($log);
       $format = 'Y-m-d H:i:s';
       $submittime = DateTime::createFromFormat($format, '2021-03-18 15:34:25');
       $turnaround = 8;
       $result = $dt->calculateDueDate($submittime, $turnaround);
       $this->assertStringContainsString('2021-03-19 15:34', $result);
     }


     /**
      * testCalculationNextSeveralDays Tests when due date is the next several day - seconds don't matter
      * @return void
      */
     public function testCalculationNextSeveralDay() :void
     {
       $log = new Log;
       $dt = new DateCalculator($log);
       $format = 'Y-m-d H:i:s';
       $submittime = DateTime::createFromFormat($format, '2021-03-18 15:34:25');
       $turnaround = 22;
       $result = $dt->calculateDueDate($submittime, $turnaround);
       $this->assertStringContainsString('2021-03-23 13:34', $result);
     }


     /**
      * testCalculationNextWeek Tests when due date is the next week - seconds don't matter
      * @return void
      */
     public function testCalculationNextWeek() :void
     {
       $log = new Log;
       $dt = new DateCalculator($log);
       $format = 'Y-m-d H:i:s';
       $submittime = DateTime::createFromFormat($format, '2021-03-18 15:34:25');
       $turnaround = 40;
       $result = $dt->calculateDueDate($submittime, $turnaround);
       $this->assertStringContainsString('2021-03-25 15:34', $result);
     }
}
