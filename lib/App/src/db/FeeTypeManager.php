<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/30/2015
 * Time: 5:15 PM
 */

namespace App\db;


use App\db\api\FeeTypeDto;

class FeeTypeManager
{
    public function __construct(array $feeTypes)
    {
        $this->fees = $feeTypes;

    }
    /**
     * @var FeeTypeDto[]
     */
    private $fees = null;

    /**
     * @param $feeCode
     * @return FeeTypeDto
     * @throws \Exception
     */
    public function getFeeType($feeCode)
    {
        if (array_key_exists($feeCode,$this->fees)) {
            return $this->fees[$feeCode];
        }
        throw new \Exception("Fee code '$feeCode' not found.");
    }

    public function getFeeTypeById($id) {
        foreach ($this->fees as $fee) {
            /**
             * @var $fee FeeTypeDto
             */
            if ($fee->feeTypeId == $id) {
                return $fee;
            }
        }
        return null;
    }

}