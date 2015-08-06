<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:32 AM
 */

namespace Tops\sys;


class TNameValuePair
{
    public static function Create($name,$value) {
        $result = new \stdClass();
        $result->Name = $name;
        $result->Value = $value;
        return $result;
    }

    public static function AddToArray(&$a,$name,$value) {
        $item = self::Create($name,$value);
        array_push($a,$item);
        return $item;
    }
}