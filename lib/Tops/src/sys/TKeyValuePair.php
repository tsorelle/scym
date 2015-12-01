<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:32 AM
 */

namespace Tops\sys;


class TKeyValuePair
{
    public $Key;
    public $Value;

    public static function Create($key,$value) {
        $result = new TKeyValuePair();
        $result->Key = $key;
        $result->Value = $value;
        return $result;
    }

    public static function AddToArray(&$a,$key,$value) {
        $item = self::Create($key,$value);
        array_push($a,$item);
        return $item;
    }
}
