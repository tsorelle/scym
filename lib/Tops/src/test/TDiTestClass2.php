<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/18/2015
 * Time: 6:01 PM
 */

namespace Tops\test;


class TDiTestClass2 {
    public $testClass;

    public function __construct(TDiTestClass $testClass) {
        $this->testClass = $testClass;
    }
}