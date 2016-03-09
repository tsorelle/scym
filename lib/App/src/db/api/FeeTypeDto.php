<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/20/2015
 * Time: 7:21 AM
 */

namespace App\db\api;


class FeeTypeDto
{
    public $feeTypeId;
    public $feeCode;
    public $feeCatagoryId;
    public $description;
    public $isCredit;
    public $unitAmount;
    public $basis;
    public $canWaive;
}