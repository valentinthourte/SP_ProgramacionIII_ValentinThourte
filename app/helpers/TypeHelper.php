<?php

class TypeHelper {
    public static function str_to_int_or_null($value) {
        return ctype_digit($value) ? intval($value) : null;
    }
    public static function str_has_value($str) {
        return $str != null && trim($str) != "";
    }
    public static function datetime_now($timezone = "America/Argentina/Buenos_Aires") {
        $dateTime = new DateTime("now", new DateTimeZone($timezone));
        return $dateTime;
    }

    public static function datetime_now_string($timezone = "America/Argentina/Buenos_Aires") {
        return TypeHelper::datetime_now($timezone)->format('Y-m-d H:i:s');
    }
}
