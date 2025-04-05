<?php

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d H:i:s', $timezone = null)
    {
        if (is_null($date)) {
            return null;
        }

        $carbon = \Carbon\Carbon::parse($date);
        
        if ($timezone) {
            $carbon = $carbon->timezone($timezone);
        }

        return $carbon->format($format);
    }
}