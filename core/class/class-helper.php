<?php

class Helper
{

    static public function turn_number_to_bool($number)
    {
        return $number == 1 ? "<p style='color: green'>Tak</p>" : "<p style='color: red'>Nie</p>";
    }

    static public function time_to_data($time)
    {
        return date('d.m.Y', $time);
    }

    static public function unserialize($data)
    {
        $data = htmlspecialchars_decode($data);
        $data = str_replace("&quot;", '"', $data);
        $data = unserialize($data);
        return $data;
    }

    static public function convert_to_time_format($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);

        return $formattedMinutes . ':' . $formattedSeconds;
    }

    static public function sort_array(&$table, $key, $type = "ASC")
    {
        if ($type == "ASC") {

            usort($table, function ($a, $b) use ($key) {
                if (is_numeric($a[$key]) && is_numeric($b[$key])) {
                    return $a[$key] - $b[$key];
                } else {
                    return strcmp($a[$key], $b[$key]);
                }
            });
        } else if ($type == "DESC") {
            usort($table, function ($a, $b) use ($key) {
                if (is_numeric($a[$key]) && is_numeric($b[$key])) {
                    return $b[$key] - $a[$key];
                } else {
                    return strcmp($b[$key], $a[$key]);
                }
            });
        }
    }

    static public function filter_array(&$table, $key, $value, $type = "EQUAL"): void
    {
        if ($type == "BIGGER") {

            $table = array_filter($table, function ($a) use ($key, $value) {
                if (is_numeric($a[$key])) {
                    return $a[$key] > $value;
                } else {
                    return strtolower($a[$key]) == strtolower($value);
                }
            });

        } else if ($type == "SMALLER") {

            $table = array_filter($table, function ($a) use ($key, $value) {
                if (is_numeric($a[$key])) {
                    return $a[$key] < $value;
                } else {
                    return strtolower($a[$key]) == strtolower($value);
                }
            });

        } else if ($type == "EQUAL") {

            $table = array_filter($table, function ($a) use ($key, $value) {
                return strtolower($a[$key]) == strtolower($value);
            });

        } else if ($type == "CONTAIN") {
            $table = array_filter($table, function ($a) use ($key, $value) {
                return str_contains(strtolower($a[$key]), strtolower($value));
            });

        }
    }

}