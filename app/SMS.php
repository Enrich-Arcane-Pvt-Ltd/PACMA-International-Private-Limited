<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    // send sms
    public static function sendSMS($number = null, $message)
    {
        if ($number != null) {

            $validatedNumber = self::validateNumber($number); // Pass number to validation function

            if ($validatedNumber === null) {
                return false; // Stop if the number is invalid
            }

            $companyName = config('app.name', 'PACMA International Private Limited');
            if (stripos($message, 'PACMA') === false && stripos($message, $companyName) === false) {
                $message = rtrim($message) . "\n- " . $companyName;
            }

            $user = "94772931237"; // SMS gateway account
            $password = "7633";
            $text = urlencode($message);

            $baseurl = "http://www.textit.biz/sendmsg";
            $url = "$baseurl/?id=$user&pw=$password&to=$validatedNumber&text=$text";
            $ret = file($url);
            $res = explode(":", $ret[0]);

            if (trim($res[0]) == "OK") {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // number validation - if the number includes 0 at the beginning and 9 digits relpace 0 with 94
    // if the number includes +94 at the beginning and 9 digits remove +
    // IF the number includes 9 digits return the number
    // if the number includes any characters other than digits return null
    // ELSE return the null
    public static function validateNumber($number)
    {
        if (preg_match('/^0\d{9}$/', $number)) {
            return '94' . substr($number, 1);
        } elseif (preg_match('/^\+94\d{9}$/', $number)) {
            return substr($number, 1);
        } elseif (preg_match('/^94\d{9}$/', $number)) {
            return $number;
        } elseif (preg_match('/^\d{9}$/', $number)) {
            return $number;
        } elseif (preg_match('/^\d{10}$/', $number)) {
            return '94' . substr($number, 1);
        } else {
            return null;
        }
    }
}
