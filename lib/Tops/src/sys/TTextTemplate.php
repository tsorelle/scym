<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 7:23 AM
 */

namespace Tops\sys;


class TTextTemplate
{
    private $delimiterLeft;
    private $delimiterRight;
    private $template;
    private static $instance;

    public function __construct($template='', $delimiterLeft = '{{', $delimiterRight = '}}')
    {
        $this->template = $template;
        $this->delimiterLeft = $delimiterLeft;
        $this->delimiterRight = $delimiterRight;
    }

    public function replace(array $tokens, $template = null) {
        $result = ($template == null) ? $this->template : $template;
        foreach($tokens as $token => $replace) {
            $search = $this->delimiterLeft.$token.$this->delimiterRight;
            $result = str_replace($search,$replace,$result);
        }
        return $result;
    }

    public static function Merge(array $tokens, $template) {
        if (!isset(self::$instance)) {
            self::$instance = new TTextTemplate();
        }
        return self::$instance->replace($tokens,$template);
    }
}