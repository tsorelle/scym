<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/8/2015
 * Time: 8:38 AM
 */

namespace Tops\test;


class TDiTestClass3 {
    private $value;
    public function __construct($value = null) {
        $this->value = $value;

    }
    public function getValue() {
        return $this->value;
    }
}