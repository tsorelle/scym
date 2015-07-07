<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/7/2015
 * Time: 7:39 AM
 */

namespace Tops\sys;


class TMemoryMessages implements IMessageContainer {

    private $messages = array();
    private $result = 0;


    public function AddMessage($messageType, $text)
    {
        $type = '';
        switch ($messageType) {
            case 1 : $type = "Error: ";
                $this->result = 3;
                break;
            case 2 : $type = "Warning: ";
                if ($this->result == 0) {
                    $this->result = 2;
                }
                break;
        }
        $this->messages[] = $type.$text;
    }

    public function AddInfoMessage($text)
    {
        $this->AddMessage(0,$text);
    }

    public function AddWarningMessage($text)
    {
        $this->AddMessage(2,$text);
    }

    public function AddErrorMessage($text)
    {
        $this->AddMessage(1,$text);
    }

    public function getMessages() {
        return $this->messages;
    }

    public function GetResult() {
        return $this->result;
    }
}