<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    public static function getHolidays($year)
    {
        $holidays = self::where('year', $year)->get();
        return $holidays;
    }

    // chcek if the date is holiday
    public static function isHoliday($date)
    {
        // if day is sunday then return false
        if (date('N', strtotime($date)) == 7) {
            return true;
        }
        $holiday = self::where('date', $date)->first();

        if($holiday){
            return true;
        }
        return false;
    }

    // return next working day if the date is holiday
    public static function getNextWorkingDay($date)
    {
        $nextDay = date('Y-m-d', strtotime($date . ' +1 day'));
        $holiday = self::isHoliday($nextDay);
        if ($holiday) {
            return self::getNextWorkingDay($nextDay);
        }
        return $nextDay;
    }

    // get the daily due date
    public static function getDailyDueDate($installmentDate, $timePeriod){
        $days = 0;
        $rounds = 1;
        $date = null;
        while(true){
            $date = date('Y-m-d', strtotime($installmentDate. ' + '.$rounds.' days'));

            $holiday = self::isHoliday($date);
            if($holiday){
                // $days -= 1;
                // dump("$days - $date - holiday");
            }
            else{
                $days += 1;
            }


            if((int)$timePeriod == $days + 1){
                break;
            }

            // break the loop if the days are greater than 300
            if($days > 1000){
                break;
            }
            $rounds += 1;
        }
        // return self::getNextWorkingDay($date);
        return $date;
    }

    // get the weekly due date
    public static function getWeeklyDueDate($installmentDate, $timePeriod){
        return  self::getDailyDueDate($installmentDate, $timePeriod * 7);
    }
}
