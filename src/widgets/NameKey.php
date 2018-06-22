<?php

namespace mate\yii\widgets;

class NameKey
{

    /**
     * @param $name
     * @return mixed|string
     * @ToDo find better way to create name key
     */
    public static function name2key($name) {
        $nameKeyReplace = [
            " " => "-",
            "/" => "-",
            "ä" => "ae",
            "ö" => "oe",
            "ü" => "ue",
            "ß" => "ss",
            "&" => "+"
        ];
        $nameKey = $name;
        $nameKey = str_replace(
            array_keys($nameKeyReplace),
            array_values($nameKeyReplace),
            $nameKey
        );
        $nameKey = strtolower($nameKey);
        return $nameKey;
    }

}