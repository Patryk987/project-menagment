<?php

namespace ModuleManager;

class ArrayToCSV
{

    public function save(&$array, &$headers, $name)
    {
        $name = $name . '-' . time() . '.csv';
        $fp = fopen('./uploads/csv/' . $name, 'w');
        $temp = [];
        foreach ($headers as $key => $value) {
            $temp[] = $key;
        }
        fputcsv($fp, $temp);

        foreach ($array as $fields) {

            $temp = [];
            foreach ($headers as $key => $value) {
                $line = "";
                if (count($value)) {

                    foreach ($value as $element) {
                        $line .= $fields[$element];
                    }

                } else {
                    $line = $fields[$value[0]];
                }
                $temp[] = $line;
            }
            fputcsv($fp, $temp);

        }

        fclose($fp);

        header("LOCATION: /uploads/csv/" . $name);

    }
}