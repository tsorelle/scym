<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/22/2015
 * Time: 2:28 PM
 */

namespace Tops\sys;
use Egulias\EmailValidator\EmailValidator;

/**
 * Class TEMailMessage
 * @package Tops\sys
 */
class TEMailMessage {

    /**
     * @var string
     */
    private $messageBody;
    /**
     * @var string
     */
    private $alternateBodyText;
    /**
     * @var TEmailAddress[]
     */
    private $recipientList;
    /**
     * @var TEmailAddress[]
     */
    private $ccList;
    /**
     * @var TEmailAddress[]
     */
    private $bccList;
    /**
     * @var TEmailAddress
     */
    private $fromAddress;
    /**
     * @var TEmailAddress
     */
    private $replyTo;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var TEMailAddress
     */
    private $returnAddress;
    /**
     * @var int
     * Use TContentType:: constance
     */
    private $contentType;
    /**
     * @var EmailValidator
     */
    private $addressValidator;
    /**
     * @var array
     * key = email address, value = error code
     */
    private $validationErrors;
    /**
     * @var array
     * key = email address, value = error code
     */
    private $validationWarnings;

    private $timeStamp;

    /**
     *
     */
    public function __construct() {
        $this->recipientList = array();
        $this->ccList = array();
        $this->bccList = array();

        $this->addressValidator = new EmailValidator();
        $this->validationErrors = array();
        $this->validationWarnings = array();
    }

    private function toEmailAddress($address,$name=null) {
        if (empty($address)) {
            return null;
        }
        $test = is_string($address);
        if ($test) {
//        if (is_string($address)) {
            if ($name == null) {
                $address = TEmailAddress::FromString($address);
            }
            else {
                $address = new TEmailAddress($address,$name);
            }
        }

        return $this->validateEmailAddress($address);
    }

    /**
     * @return array
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }

    /**
     * @return array
     */
    public function getValidationWarnings() {
        return $this->validationWarnings;
    }

    /**
     * @return bool
     */
    public function hasErrors() {
        return empty($this->validationErrors);
    }

    /**
     * @return bool
     */
    public function hasWarnings() {
        return empty($this->validationWarnings);
    }

    /**
     * @param array $sourceList
     * @param $emailAddress
     * @return bool
     */
    private function removeAddress(array $sourceList, $emailAddress) {
        $index = $this->findAddress($sourceList, $emailAddress);
        if ($index === false)
            return false;
        unset($sourceList[$index]);
        return true;
    }

    /**
     * @param array $list
     * @param $searchAddress
     * @return bool|int
     */
    private function findAddress(array $list, $searchAddress) {
        if (empty($list))
            return false;

        for ($i=0; $i<sizeof($list); $i++) {
            if (self::AddressEquals($list[$i],$searchAddress))
                return $i;
        }
        return false;
    }

    /**
     * @param TEmailAddress $address
     * @param string $emailAddress
     * @return bool
     */
    public static function AddressEquals(TEmailAddress $address, $emailAddress) {
        return $address->getAddress() == $emailAddress;
    }

    /**
     * @param $emailAddress
     * @return bool
     */
    private function validateAddress($emailAddress) {
        if (empty($emailAddress)) {
            return false;
        }
        $isValid = $this->addressValidator->isValid($emailAddress);
        if ($this->addressValidator->hasWarnings()) {
            $this->validationWarnings[@$emailAddress] = $this->addressValidator->getWarnings();
        }
        if ($isValid) {
            return true;
        }
        $this->validationErrors[$emailAddress] = $this->addressValidator->getError();
        return false;
    }

    /**
     * @param TEmailAddress[] $list
     * @param TEmailAddress $emailAddress
     * @return bool
     * @internal param $name
     */
    public function addAddress(Array &$list, TEmailAddress $emailAddress) {

        $found = $this->findAddress($list,$emailAddress->getAddress());
        if ($found) {
           $list[$found] = $emailAddress;
        }
        else {
            array_push($list,$emailAddress);
        }
        return $found;
    }


    public function validateEmailAddress(TEmailAddress $emailAddress) {
        $isValid = $this->validateAddress($emailAddress->getAddress());
        if ($isValid ) {
            return $emailAddress;
        }
        return null;
    }



    /**
     * @param $recipient
     * @param string $name
     * @return bool
     */
    public function addRecipient($recipient, $name=null)
    {
        $recipient = $this->toEmailAddress($recipient,$name);
        if ($recipient === null) {
            return false;
        }
        if ($this->removeAddress($this->bccList, $recipient->getAddress()) === false) {
            $this->removeAddress($this->ccList, $recipient->getAddress());
        }
        $this->addAddress($this->recipientList, $recipient, $name,$this->ccList);
        return true;
    }

    /**
     * @param $recipient
     * @param string $name
     * @return bool
     */
    public function addCC($recipient, $name='')
    {
        $recipient = $this->toEmailAddress($recipient,$name);
        if ($recipient === null) {
            return false;
        }

        if ($this->findAddress($this->recipientList,$recipient->getAddress()) !== false) {
            return true;
        }
        if ($this->findAddress($this->bccList,$recipient->getAddress()) !== false) {
            return true;
        }
        $this->addAddress($this->ccList, $recipient, $name);
        return true;
    }

    /**
     * @param $recipient
     * @param string $name
     * @return bool
     */
    public function addBCC($recipient, $name='')
    {
        $recipient = $this->toEmailAddress($recipient,$name);
        if ($recipient == null) {
            return false;
        }
        $this->removeAddress($this->ccList,$recipient);
        $this->addAddress($this->bccList, $recipient, $name,$this->ccList);
        return true;
    }

    /**
     * @param $recipients
     * @param $name
     * @return bool
     * @internal param $recipient
     */
    public function setRecipient($recipients, $name=null)
    {
        $this->recipientList = Array();
        if (is_string($recipients)) {
            $recipients = explode(';',$recipients);
        }

        if (is_array($recipients)) {
            foreach($recipients as $recipient) {
                $this->addRecipient($recipient);
            }
        }
        else {
            $this->addRecipient($recipients, $name);
        }
        return sizeof($this->recipientList);
    }  //  setRecipient

    /**
     * @param $sender
     * @param null $name
     * @return bool
     */
    public function setFromAddress($sender, $name=null)
    {
        $sender = $this->toEmailAddress($sender,$name);
        if ($sender != null) {
            $this->fromAddress = $sender;
            return true;
        }
        return false;
    }  //  setFromAddress



    /**
     * @param $address
     * @param null $name
     * @return bool
     */
    public function setReturnAddress($address, $name=null)
    {
        $address = $this->toEmailAddress($address,$name);
        if ($address != null) {
            $this->returnAddress = $address;
            return true;
        }
        return false;
    }  //  setReturnAddress

    /**
     * @param $address
     * @param null $name
     * @return bool
     */
    public function setReplyTo($address, $name=null)
    {
        $address = $this->toEmailAddress($address,$name);
        if ($address != null) {
            $this->fromAddress = $address;
            return true;
        }
        return false;
    }  //  setReturnAddress

    /**
     * @param $value
     */
    public function setSubject($value)
    {
        $this->subject = stripslashes($value);
    }  //  setSubject

    /**
     * @param $string
     * @return bool
     */
    public function containsScriptTags($string) {
        $badTags = array('</script>','</object>','</applet>');
        $string = preg_replace('/\s+/', '', $string);
        foreach($badTags as $tag) {
            if (stripos($string, $tag)) {
                return true;
            };
        }
        return false;
    }

    /**
     * @param $value
     * @param string $contentType
     */
    public function setMessageBody($value, $contentType='text')
    {
        if ($contentType == 'html') {
            $this->setHtmlMessageBody($value);
        }
        else {
            $this->setMessageText($value);
        }
    }  //  setMessageBody


    public function setMessageText($value) {
        if ($this->containsScriptTags($value)) {
            // attempt to insert executable html in message
            // don't take chances. Strip all tags.
            $value = strip_tags($value);
        }
        $value = str_replace ("\r\n", "\n", $value);
        $this->messageBody = stripslashes($value);
    }

    /**
     * @param $text
     * @param bool $setTextPart
     */
    public function setHtmlMessageBody($text, $setTextPart = true)
    {
        $this->setMessageBody($text);
        if ($setTextPart) {
            $this->setAlternateBodyText($text);
        }
        else {
            $this->contentType = TContentType::$Html;
        }
    }  //  setAlternateBodyText


    /**
     * @param $text
     */
    public function setAlternateBodyText($text)
    {
        $this->alternateBodyText = strip_tags( $text );
        $this->contentType = TContentType::$MultiPart;
    }  //  setAlternateBodyText


    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @return TEmailAddress
     */
    public function getFromAddress() {
        return $this->fromAddress;
    }

    /**
     * @param $address
     * @param null $name
     */
    public function setSender($address, $name=null) {
        $this->fromAddress = new TEmailAddress($address, $name);
    }

    /**
     * @return array|TEmailAddress[]
     */
    public function getRecipientList() {
        return $this->recipientList;
    }


    /**
     * @param array $list
     * @return array
     */
    private function addressesToArray(array $list) {
        $result = array();
        foreach ($list as $email) {
            $this->addAddressToArray($result, $email);
        }
        return $result;
    }

    /**
     * @param array $list
     * @param TEmailAddress $email
     */
    private function addAddressToArray(array &$list, TEmailAddress $email) {
        $name = $email->getName();
        if (empty($name)) {
            array_push($list,$email->getAddress());
        }
        else {
            $list[$email->getAddress()] = $name;
        }
    }

    /**
     * @param $list
     * @return string
     */
    private function addressesToString($list) {
        return implode(';',$list);
    }

    /**
     * @return array|TEmailAddress[]
     */
    public function getRecipients() {
        return $this->recipientList;
    }

    /**
     * @return array
     */
    public function getRecipientsAsArray() {
        return $this->addressesToArray( $this->recipientList );
    }

    /**
     * @return string
     */
    public function getRecipientsAsString() {
        return $this->addressesToString($this->recipientList);
    }

    /**
     * @return array|TEmailAddress[]
     */
    public function getCCs() {
        return $this->ccList;
    }

    /**
     * @return array
     */
    public function getCCsAsArray() {
        return $this->addressesToArray($this->ccList);
    }

    /**
     * @return string
     */
    public function getCCsAsString() {
        return $this->addressesToString($this->ccList);
    }

    /**
     * @return array|TEmailAddress[]
     */
    public function getBCCs() {
        return $this->bccList;
    }

    /**
     * @return array
     */
    public function getBCCsAsArray() {
        return $this->addressesToArray($this->bccList);
    }

    /**
     * @return string
     */
    public function getBCCsAsString() {
        return $this->addressesToString($this->bccList);
    }

    /**
     * @return TEMailAddress
     */
    public function getReturnAddress() {
        if (empty($this->returnAddress))
            return $this->fromAddress;
        return $this->returnAddress;
    }

    /**
     * @return TEmailAddress
     */
    public function getReplyTo() {
        if (empty($this->replyTo))
            return $this->fromAddress;
        return $this->replyTo;
    }

    /**
     * @return int
     */
    public function getContentType() {
        if (!isset($this->contentType))
            $this->contentType = TContentType::$Text;
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getMessageBody() {
        return $this->messageBody;
    }

    /**
     * @return string
     */
    public function getTextPart() {
        if (empty($this->alternateBodyText))
            return strip_tags($this->messageBody);
        return $this->alternateBodyText;
    }

    public function getAddressCount() {
        return
            sizeof($this->recipientList) +
            sizeof($this->ccList) +
            sizeof($this->bccList);
    }

    public function setTimeStamp($value) {
        $this->timeStamp = $value;
    }

    public function getTimeStamp() {
        if (empty($this->timeStamp) ) {
            $this->timeStamp = time();
        }
        return $this->timeStamp;
    }

} // TMailMessage