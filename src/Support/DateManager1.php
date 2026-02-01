<?php

class DateManager1
{
    static $date_asNumber = 'd-m-Y';
    static $date_asText = 'd D M Y';
    static $dateInverse_asNumber = 'Y-m-d';
    static $dateInverse_asText = 'Y-M-D';

    static $time_asAmPm = 'g:i a';
    static $time_as24Hours = 'h:i:s';

    static $dateTime_asNumber = 'd-m-Y h:i:s';
    static $dateTimeInverse_asNumber = 'Y-m-d h:i:s';
    static $database_timeStamp = 'Y-m-d h:i:s';
    static $dateTime_asText = 'l jS F Y,  g:i a';


    /**
     * @return mixed
     */
    static function carbon()
    {
        return carbonDate();
    }

    /**
     * @param $date
     * @return mixed
     */
    static function carbonParse($date)
    {
        return static::carbon()::parse($date);
    }

    /**
     * @param $date
     * @return string
     */
    static function diffForHumans($date)
    {
        return static::carbon()::parse($date)->diffForHumans();
    }


    /**
     * @param string $format
     * @param null|int $timeStamp
     * @param bool $timeStampStrictMode
     * @return false|int|string
     */
    static function date($format = 'd-m-Y h:i:s', $timeStamp = null, $timeStampStrictMode = true)
    {
        if ($timeStamp && $timeStampStrictMode && $timeStamp <= 0) return 0;
        return date($format, $timeStamp);
    }

    static function convert24HoursTime_toAmPm($time = '')
    {
        return date("g:i A", strtotime($time));
    }

    static function convertAmPmTime_to24Hours($time = '')
    {
        return date("G:i", strtotime($time));
    }

    static function now($pretty = false)
    {
        return $pretty ? self::prettyDateTime(self::now(false)) : date(self::$database_timeStamp);
    }

    static function nowDate($pretty = false)
    {
        return $pretty ? date(self::$dateInverse_asText) : date(self::$dateInverse_asNumber);
    }

    static function nowTime($pretty = false)
    {
        return $pretty ? date(self::$time_asAmPm) : date(self::$time_as24Hours);
    }


    static function prettyDateTime($date = null)
    {
        $date = $date ? $date : self::now();
        $x = explode('-', $date);
        $a = $x[0];
        $m = $x[1];
        $c = $x[2];
        if (strlen($c) > 2) {
            $y = $c;
            $d = $a;
        } else if (strlen($a) > 2) {
            $y = $a;
            $d = $c;
        } else return $date;
        $mon = "";
        switch ($m) {
            case '01':
                $mon = "Jan";
                break;
            case '02':
                $mon = "Feb";
                break;
            case '03':
                $mon = "Mar";
                break;
            case '04':
                $mon = "Apr";
                break;
            case '05':
                $mon = "May";
                break;
            case '06':
                $mon = "Jun";
                break;
            case '07':
                $mon = "Jul";
                break;
            case '08':
                $mon = "Aug";
                break;
            case '09':
                $mon = "Sep";
                break;
            case '10':
                $mon = "Oct";
                break;
            case '11':
                $mon = "Nov";
                break;
            case '12':
                $mon = "Dec";
                break;
        }
        return "$d $mon, $y";
    }

    static function getWeekDayName($date = null)
    {
        $date = $date ? $date : self::nowDate(false);
        $arr = explode('-', $date);
        $d = $arr[2];
        $m = $arr[1];
        $y = $arr[0];
        $tot = $feb = $sum = 0;
        if ($y % 4 == 0) {
            $tot = 366;
            $feb = 29;
        } else {
            $tot = 365;
            $feb = 28;
        }

        $mon = array(31, $feb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        for ($i = 0; $i < $m - 1; $i++) {
            $sum += $mon[$i];
        }
        $dd = $d - 1;
        $sum += $dd;
        if ($y > 1) $dy = $y - 1;
        $ly = $dy / 4;
        $nly = $dy - $ly;
        $ly *= 366;
        $nly *= 365;
        $sum += $ly + $nly;
        $res = $sum % 7;

        switch ($res) {
            case 0:
                return "Sunday";
            case 1:
                return "Monday";
            case 2:
                return "Tuesday";
            case 3:
                return "Wednesday";
            case 4:
                return "Thursday";
            case 5:
                return "Friday";
            case 6:
                return "Saturday";
        }
        return '';
    }

    private $time = null;
    private $timeCompare = null;


    /**
     *
     *
     *
     *
     * $diff  = new DateManager1( '2018-05-31 22:01:14' ); // OR pass in strtotime('2018-05-31 22:01:14')
     * echo $diff->isTimeElapsed()? 'Time Up': $diff->getRemainingTime_asText();
     *
     *
     *
     *
     * DateManager1 constructor.
     * @param string $dataBaseTimeStamp
     * @param null $compareDate_defaultIsNow @default is Now()
     * @param string $dateFormat
     *
     */
    function __construct($dataBaseTimeStamp = '1988-08-10', $compareDate_defaultIsNow = null, $dateFormat = "U = Y-m-d H:i:s")
    {
        try {
            // is TimeStamp or Use as String
            $this->time = new DateTime();
            if ($dataBaseTimeStamp) $this->time = self::normalizeDateOrTimestamp_to_DateTime($dataBaseTimeStamp, $dateFormat);

            // compare
            $this->timeCompare = new DateTime();
            if ($compareDate_defaultIsNow) $this->timeCompare = self::normalizeDateOrTimestamp_to_DateTime($compareDate_defaultIsNow, $dateFormat);
        } catch (Exception $ex) {
            throw new Exception('Bad Date Format : ' . $ex->getMessage());
        }
    }

    /**
     * Know if Time is Up
     *  $dataBaseTimeStamp - $compareDate_defaultIsNow
     * @return bool
     */
    function isTimeElapsed()
    {
        if (!$this->time || !$this->timeCompare || (($this->timeCompare->getTimestamp() - $this->time->getTimestamp()) <= 0)) return false; else return true;
    }

    /**
     * @param string|int $time
     * @param string $dateFormat
     * @return DateTime|string
     */
    static function normalizeDateOrTimestamp_to_DateTime($time = '2007-02-14 20:25:25', $dateFormat = "U = Y-m-d H:i:s")
    {
        $data = new DateTime();
        if (is_numeric($time)) $data->setTimestamp($time);
        else {
            $data = new DateTime($time);
            $data->format($dateFormat);
        }
        return $data;
    }


    /**
     * echo getRemainingTime() //'Your age is %Y years and %d days' // Your age is 28 years and 19 days
     * @return DateInterval|false|int|null
     */
    function getRemainingTime_asDateInterval()
    {
        if ($this->isTimeElapsed()) return null;
        return date_diff($this->time, $this->timeCompare);
    }

    function getTotalDays()
    {
        if ($this->isTimeElapsed()) return 0;
        return date_diff($this->time, $this->timeCompare)->days;
    }

    function getTotalHours()
    {
        if ($this->isTimeElapsed()) return 0;
        return ($this->getTotalDays() > 0) ? ($this->getRemainingTime_asDateInterval()->h + ($this->getTotalDays() * 24)) : $this->getRemainingTime_asDateInterval()->h;
    }


    /**
     * echo getRemainingTime_asText() // Output: The difference is 28 years, 5 months, 19 days, 20 hours, 34 minutes, 36 seconds
     * @param string $defaultTimeElapseText
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    function getRemainingTime_asText($defaultTimeElapseText = 'Time Up ##:##:##', $prefix = ' ', $suffix = ', ')
    {
        if ($this->isTimeElapsed()) return $defaultTimeElapseText;
        $diff = $this->diff();
        $time = String1::ifNotEmpty($diff->y, " $prefix" . $diff->y . " " . String1::pluralize_if($diff->y, 'year', 'years') . $suffix);
        $time .= String1::ifNotEmpty($diff->m, " $prefix" . $diff->m . " " . String1::pluralize_if($diff->m, 'month', 'months') . $suffix);
        $time .= String1::ifNotEmpty($diff->d, " $prefix" . $diff->d . " " . String1::pluralize_if($diff->d, 'day', 'days') . $suffix);
        $time .= String1::ifNotEmpty($diff->h, " $prefix" . $diff->h . " " . String1::pluralize_if($diff->h, 'hour', 'hours') . $suffix);
        $time .= String1::ifNotEmpty($diff->i, " $prefix" . $diff->i . " " . String1::pluralize_if($diff->i, 'minute', 'minutes') . ' ');
        return trim($time, ', ');
    }

    function diff()
    {
        return date_diff($this->time, $this->timeCompare);
    }

    /**
     * echo getRemainingTime_asText() // Output: The difference is 28 years, 5 months, 19 days, 20 hours, 34 minutes, 36 seconds
     * @return string
     */
    function getRemainingTime_asTimeStamp()
    {
        if ($this->isTimeElapsed()) return 0;
        $remDiff = $this->time->diff($this->timeCompare);
        return $remDiff->format('%a');
        //return strtotime($this->getRemainingTime_asText('', '+', ' '));
    }


    /************************************************************************************************************************************************************************************/
    /*
     *
     *      Static
     *
            $from = (time() + (5 * 60 * 60));
            $fix = (time() + (3 * 60 * 60));
            echo DateManager1::getRemainingTime($from, $fix);
     *
     */
    /************************************************************************************************************************************************************************************/


    /************************************************************************************************************************************************************************************
     *
     *  Check if time elapse, i.e ($fromTime set in DataBase of fix somewhere) - (current time) > futureTimePassingIn as Hours, Days, Weeks
     *  Note That
     *        strtotime('+2 hour')
     *          is the same as time() + (2 * 60 * 60)
     *
     *        strtotime('+2 days') thesame as time() + (2 * 3600)
     *
     * @param int $dbFixTime
     * @param int $minuteAfter
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @return bool
     *
     *      echo( strtotime("now") . "<br>");
     *      echo( strtotime("now") . "<br>");
     *      echo( strtotime("3 October 2005") . "<br>");
     *      echo( strtotime("+5 hours") . "<br>");
     *      echo( strtotime("+1 week") . "<br>");
     *      echo( strtotime("+1 week 3 days 7 hours 5 seconds") . "<br>");
     *      echo( strtotime("next Monday") . "<br>");
     *      echo( strtotime("last Sunday"));
     */
    static function isTimeElapse($dbFixTime = 0, $minuteAfter = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0)
    {
        return ($dbFixTime) < strtotime(self::dateTimeNormalizer($minuteAfter, $hoursAfter, $daysAfter, $weeksAfter));
    }

    /**
     *  If $dbFixTime less that $compareFutureTime already
     *
     * @param int $dbFixTime
     * @param $compareFutureTime
     * @return bool
     */
    static function isElapse($dbFixTime = 0, $compareFutureTime = null)
    {
        return ($dbFixTime < $compareFutureTime);
    }

    static function getDaysFrom($dbTimeStamp, $nowTimeStamp = null)
    {
        $str = (($nowTimeStamp) ? $nowTimeStamp : strtotime(date("M d Y "))) - ($dbTimeStamp);
        return floor($str / 3600 / 24);
    }

    /**
     *  Get Remaining Time after Subtracting $dbFixTime. Alternative to @param int $dbFixTime
     * @param int $dbFixTime
     * @param int $minuteAfter
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @return int
     * @see DateManager1::removeDateTime()
     *
     */
    static function getRemainingTime($dbFixTime = 0, $minuteAfter = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0)
    {
        $time = ($dbFixTime - strtotime(self::dateTimeNormalizer($minuteAfter, $hoursAfter, $daysAfter, $weeksAfter)));
        return ($time < 0 ? 0 : $time);
    }

    /**
     *
     *  strtotime() Normaliser.
     *  return some format like +2 months +1 week +3 days + 2 hours + 0 minute
     *
     * @param string $symbol , + or -
     * @param int $minute
     * @param int $hoursAfter
     * @param int $daysAfter
     * @param int $weeksAfter
     * @param int $month
     * @return string
     */
    static function dateTimeNormalizer($symbol = '+', $minute = 0, $hoursAfter = 0, $daysAfter = 0, $weeksAfter = 0, $month = 0)
    {
        $pie = $month > 0 ? "{$symbol}{$month} " . String1::pluralize_if($month, 'month', 'months') . " " : "";
        $pie .= $weeksAfter > 0 ? "{$symbol}{$weeksAfter} " . String1::pluralize_if($weeksAfter, 'week', 'weeks') . " " : "";
        $pie .= $daysAfter > 0 ? "{$symbol}{$daysAfter} " . String1::pluralize_if($daysAfter, 'day', 'days') . " " : "";
        $pie .= $hoursAfter > 0 ? "{$symbol}{$hoursAfter} " . String1::pluralize_if($hoursAfter, 'hour', 'hours') . " " : "";
        $pie .= $minute > 0 ? "{$symbol}{$minute} " . String1::pluralize_if($minute, 'minute', 'minutes') : "";
        return $pie;
    }


    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return string
     */
    static function addDateTime_asDatabaseTimeStamp($minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        return date(self::$dateTimeInverse_asNumber, \DateManager1::addDateTime(null, $minute, $hours, $days, $weeks));
    }

    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return string
     */
    static function removeDateTime_asDatabaseTimeStamp($minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        return date(self::$dateTimeInverse_asNumber, \DateManager1::removeDateTime(null, $minute, $hours, $days, $weeks));
    }

    /**
     * Add Some Minute, Hours... to  $initTime Date
     *
     * @param null|int $initTime @default time()
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function addDateTime($initTime = null, $minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        $initTime = $initTime ? $initTime : time();
        $time = strtotime(self::dateTimeNormalizer('+', $minute, $hours, $days, $weeks), $initTime);
        return $time;
    }


    /**
     * Remove Some Minute, Hours... from  $initTime Date
     *
     * @param null|int $initTime @default time()
     * @param int $minute
     * @param int $hours
     * @param int $days
     * @param int $weeks
     * @return int
     */
    static function removeDateTime($initTime = null, $minute = 0, $hours = 0, $days = 0, $weeks = 0)
    {
        $initTime = $initTime ? $initTime : time();
        $time = strtotime(self::dateTimeNormalizer('-', $minute, $hours, $days, $weeks), $initTime);
        return $time;
    }
}
