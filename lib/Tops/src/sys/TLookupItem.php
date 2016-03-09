<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/9/2015
 * Time: 6:56 AM
 */

namespace Tops\sys;


class TLookupItem
{

    /*
     * Service contract:
     *     export interface ILookupItem {
     *          Key: any;
     *          Text: string;
     *          Description: string;
     *      }
     */

    public static function Create($key,$text,$description='') {
        $result = new \stdClass();
        $result->Key = $key;
        $result->Text = $text;
        $result->Description = $description;
        return $result;
    }

    public static function AddToArray(&$a,$key,$text,$description='') {
        $item = self::Create($key,$text,$description);
        array_push($a,$item);
        return $item;
    }
}