<?php

class StringHelper {
    public static function isNullOrEmpty($string) {
        return (!isset($string) || trim($string) === '');
    }
}