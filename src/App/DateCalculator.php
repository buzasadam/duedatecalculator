<?php

namespace App;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use Other\Log;

/**
 * DateCalculator  Emarsys homework - Due Date Calculator
 *                 WORKDAY_START and WORKDAY_END can be flexible, but the solution doesn't handle that situation
 *                 when submit time is out of working time period
 *
 * @author Ádám Buzás
 */
class DateCalculator {

    const WORKDAY_START = 9;
    const WORKDAY_END = 17;
    const TIME_FORMAT = 'Y-m-d H:i';

    private $minutes = 0;
    private $hours = 0;

    private $log;


    /**
     * __construct receiving outside parameters
     * @param Log $log log handler instance
     */
    public function __construct(Log $log)
    {
      $this->log = $log;
    }

    /**
     * calculateDueDate Incoming data prepare and running due date calculation
     *
     * @param  DateTime $submittime     submit date and time
     * @param  Int      $turnaroundtime turnaround time in hours
     * @return string                   due date and time formatted with TIME_FORMAT
     */
	  public function calculateDueDate(DateTime $submittime,Int $turnaroundtime) {

        $this->log->m_log('Use log inside');

        $this->minutes = $turnaroundtime*60;

        $this->hours = $turnaroundtime;

        $submittime = $this->submittimeCorrection($submittime);

        $submittime->setTime($submittime->format('H'), $submittime->format('i'), 0);

        return $this->calculateDate($submittime, $turnaroundtime);
	  }


    /**
     * submittimeCorrection Correcting submittime, when it is out of working day
     *
     * @param  DateTime $submittime submit date and time
     * @return DateTime             submit date and time corrected to workday period
     */
    public function submittimeCorrection(DateTime $submittime) :DateTime
    {
        $year = $submittime->format('Y');
        $month = $submittime->format('m');
        $day = $submittime->format('d');

        $workdaystart = new DateTime();
        $workdaystart->setTimeZone(new DateTimeZone('Europe/Budapest'));
        $workdaystart->setTime(self::WORKDAY_START,0);
        $workdaystart->setDate($year,$month,$day);

        $workdayend = new DateTime();
        $workdayend->setTimeZone(new DateTimeZone('Europe/Budapest'));
        $workdayend->setTime(self::WORKDAY_END,0);
        $workdayend->setDate($year,$month,$day);

        if ($submittime < $workdaystart)
        {
          $submittime = $workdaystart;
          $submittime = $this->nextWorkDay($submittime);
        }

        if ($submittime > $workdayend)
        {
          $submittime = $workdaystart->add(new DateInterval("PT24H"));
          $submittime = $this->nextWorkDay($submittime);
        }

        return $submittime;
    }

    /**
     * timeToEndOfDay  Calculates the remaining time to the end of the workday
     *
     * @param  DateTime     $submittime submit date and time
     * @return DateInterval             Remaining time
     */
    public function timeToEndOfDay(DateTime $submittime) :DateInterval
    {
        $targettime = new DateTime();
        $targettime->setTimezone(new DateTimeZone('Europe/Budapest'));
        $targettime->setTime(self::WORKDAY_END,0);

        return $submittime->diff($targettime);
    }

    /**
     * minutesToEndOfDay  Calculates the remaining time to the end of the workday in minutes
     * @param  DateInterval $time remaining time to the end of the workday
     * @return Int                remaining time in minutes
     */
    public function minutesToEndOfDay(DateInterval $time) :Int
    {
         return $time->h*60+$time->i;
    }

    /**
     * daysToShift   Calculates how many days have to shift the due date depending on workday start and end time
     *
     * @param  Int    $leftminutes total left minutes of the period excluding the current day
     * @return array  $result      array containing days to shift and remaining time for the last working day of period
     */
    public function daysToShift(Int $leftminutes) :array
    {
       $days = floor(($leftminutes/((self::WORKDAY_END-self::WORKDAY_START)*60)));
       $result['days'] = $days + 1;
       $result['minutes'] = $leftminutes - ($days * 60 * (self::WORKDAY_END-self::WORKDAY_START));

       return $result;
    }

    /**
     * calculateDate  Calculates the due date whether have to shift working day or not. If due date would be happen on weekend days then
     *                automatically shift to next day (till the nex workday)
     *
     * @param  DateTime $submittime     submit date and time
     * @param  Int      $turnaroundtime turnaround time in hours
     * @return string                   due date and time formatted with TIME_FORMAT
     */
    public function calculateDate(DateTime $submittime, Int $turnaroundtime)  :String
    {
      $leftofday = $this->timeToEndOfDay($submittime);
      $leftminutes = $this->minutesToEndOfDay($leftofday);

      if ($leftminutes > $this->minutes)
      {
          $duetime = $submittime->add(new DateInterval("PT{$turnaroundtime}H"));
          return $duetime->format(self::TIME_FORMAT);
      }
      else {

        $shift = $this->daysToShift($this->minutes - $leftminutes);
        $hours = $shift['days']*24;
        $minutes = $shift['minutes'];
        $starttime = new DateTime();
        $starttime->setTimezone(new DateTimeZone('Europe/Budapest'));
        $starttime->setTime(self::WORKDAY_START,0);

        for ($i = 1; $i<=$shift['days']; $i++) {
            $starttime->add(new DateInterval("PT24H"));
            $starttime = $this->nextWorkDay($starttime);
        }
        $duetime = $starttime->add(new DateInterval("PT{$minutes}M"));

        return $duetime->format(self::TIME_FORMAT);
      }
    }

    /**
     * nextWorkDay  finds the nex workday depending on received current date and time
     * @param  DateTime $time current date and time
     * @return DateTime $time next workday id needed 
     */
    public function nextWorkDay(DateTime $time)
    {
        if ($time->format('N') == 6)
        {
            $time->add(new DateInterval("PT48H"));
        }

        if ($time->format('N') == 7)
        {
            $time->add(new DateInterval("PT24H"));
        }

        return $time;
    }
}
