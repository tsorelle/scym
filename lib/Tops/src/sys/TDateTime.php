<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/27/2015
 * Time: 7:02 AM
 */

namespace Tops\sys;


class TDateTime
{
    /**
     * Format DateTime object as string for display
     *
     * @param \DateTime $dateValue
     * @param string $format
     * @return string
     */
    public static function format($dateValue,$format='F j, Y')
    {
        if (empty($dateValue) || $dateValue->format('Y-m-d H:i:s') == '0000-01-01 00:00:00') {
            return '';
        }
        return $dateValue->format($format);
    }

    public static function isEmpty($dateValue) {
        return (empty($dateValue) || $dateValue->format('Y-m-d H:i:s') == '0000-01-01 00:00:00');
    }

    public static function formatRange(\DateTime $startDate, \DateTime $endDate = null, $includeDOW = false) {
        if (self::isEmpty($endDate)) {
            if (self::isEmpty($startDate)) {
                return '';
            }
            $fmt = $includeDOW ? 'l F jS Y' : 'F jS Y';
            return $startDate->format($fmt);
        }
        $newMonth = $includeDOW || $startDate->format('m') != $endDate->format('m');
        $newYear = $startDate->format('Y') != $endDate->format('Y');
        $startFmt = $includeDOW ? 'l F jS' : 'F jS';
        if ($newYear) {
            $startFmt .= ", Y";
        }
        $endFmt = $includeDOW ? 'l ' : '';
        $endFmt .= $newMonth ?  'F jS, Y' : 'jS, Y';
        return $startDate->format($startFmt).' to '.$endDate->format($endFmt);
    }

    public static function getMonth(\DateTime $dateValue) {
        return date('m',$dateValue);
    }
}