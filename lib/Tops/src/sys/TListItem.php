<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2015
 * Time: 3:48 PM
 */

namespace Tops\sys;


class TListItem
{
    public static function Create($text,$value,$desctiption = '') {
        $result = new \stdClass();
        $result->Text = $text;
        $result->Value =  $value;
        $result->Description =$desctiption;
        return $result;
    }

    public static function AddToArray(&$a,$text,$value,$desctiption = '') {
        $item = self::Create($text,$value,$desctiption);
        array_push($a,$item);
        return $item;
    }

}