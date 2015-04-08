<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/23/2015
 * Time: 2:56 PM
 */

namespace Tops\sys;


/**
 * Manages email operations
 * Class TPostOffice
 * @package Tops\sys
 */
class TPostOffice {
    private static $instance;

    /**
     * @return TPostOffice
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = TObjectContainer::Get('postOffice');
        }
        return self::$instance;
    }

    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }


    /**
     * @var IMailer
     */
    private $mailer;
    /**
     * @var IMailboxManager
     */
    private $mailboxes;

    public function __construct(IMailer $mailer, IMailboxManager $mailboxes) {
        $this->mailboxes = $mailboxes;
        $this->mailer = $mailer;
    }

    public static function CreateMessageToUs($addressId='support')
    {
        return self::getInstance()->_createMessageToUs($addressId);
    }
    private function _createMessageToUs($addressId='support')
    {
        $result = new TEMailMessage();

        $recipients = explode(',',$addressId);
        $count = 0;
        foreach ($recipients as $addressId) {
            $mailbox = $this->mailboxes->findByCode($addressId);
            if ($mailbox != null) {
                $result->addRecipient($mailbox->getEmail(),$mailbox->getName());
                $count++;
            }
        }
        if ($count == 0) {
            throw new \Exception('No mailboxes found.');
        }
        return $result;
    }


    public static function GetMailboxAddress($addressId)
    {
        $repository = self::getInstance()->mailboxes;
        return $repository->findByCode($addressId);
    }

    public static function CreateMessageFromUs($addressId='support',$subject=null,$body=null,$contentType='text') {
        return self::getInstance()->_createMessageFromUs($addressId,$subject,$body,$contentType);
    }

    private function _createMessageFromUs($addressId='support',$subject=null,$body=null,$contentType='text')
    {

        // TTracer::Trace("CreateMessageFromUs($addressId) address: $address; name: $identity");
        $bounce = $this->mailboxes->findByCode('bounce');

        $result = new TEMailMessage();
        $mailbox = $this->mailboxes->findByCode($addressId);
        if ($mailbox == null) {
            return null;
        }
        $result->setFromAddress($mailbox->getEmail(), $mailbox->getName());
        $result->setReturnAddress($bounce);
        if (!empty($subject))
            $result->setSubject($subject);
        if (!empty($body)) {
            $result->setMessageBody($body,$contentType);
        }
        return $result;
    }  //  newEmailMessageFromUs


    public static function Send($message) {
        // TTracer::ShowArray($message);
        // TTracer::Trace('Send to: '.htmlentities($message->getRecipients()));
        return self::getInstance()->_send($message);
    }
    private function _send($message) {
        return $this->mailer->send($message);
    }


    public static function SendMessage($to, $from, $subject, $bodyText, $contentType='text', $bounce = null)
    {
        //TTracer::Trace('SendMessage');
        return self::getInstance()->_sendMessage($to, $from, $subject, $bodyText, $contentType, $bounce);
    }
    private function _sendMessage($to, $from, $subject, $bodyText, $contentType='text', $bounce = null) {

        $message = new TEMailMessage();
        $message->setRecipient($to);
        $message->setFromAddress($from);
        $message->setSubject($subject);
        $message->setMessageBody($bodyText,$contentType);
        if ($bounce) {
            $message->setReturnAddress($bounce);
        }
        return $this->mailer->send($message);
    }

    public static function SendMessageToUs($fromAddress, $subject, $bodyText, $addressId='admin')
    {
        return self::getInstance()->_sendMessageToUs($fromAddress, $subject, $bodyText, $addressId);
    }
    private function _sendMessageToUs($fromAddress, $subject, $bodyText, $addressId='admin')
    {
        $message = $this->_createMessageToUs($addressId);
        $message->setFromAddress($fromAddress);
        $message->setSubject($subject);
        $message->setMessageBody($bodyText);
        $message->setReturnAddress($fromAddress);
        return $this->mailer->send($message);
    }

    public static function SendMessageFromUs($recipients, $subject, $bodyText, $addressId='admin', $contentType='html' ) {
        return self::getInstance()->_sendMessageFromUs($recipients, $subject, $bodyText, $addressId, $contentType);
    }

    private function _sendMessageFromUs($recipient, $subject, $bodyText, $addressId='admin', $contentType='html'  ) {
        // TTracer::Trace('SendMessageFromUs');
        $message = $this->_createMessageFromUs($addressId, $subject, $bodyText, $contentType);
        $message->setRecipient($recipient);
        return $this->mailer->send($message);
    }

    public static function SendHtmlMessageFromUs($recipients, $subject, $bodyText, $addressId='support' ) {
        return self::getInstance()->_sendHtmlMessageFromUs($recipients, $subject, $bodyText, $addressId);
    }
    private function _sendHtmlMessageFromUs($recipients, $subject, $bodyText, $addressId='support' ) {
        $message = $this->_createMessageFromUs($addressId, $subject, $bodyText, 'html');
        $message->setRecipient($recipients);
        return $this->mailer->send($message);
    }

    public static function SendMultiPartMessageFromUs($recipients, $subject, $bodyText, $textPart, $addressId='support' ) {
        return self::getInstance()->_sendMultiPartMessageFromUs($recipients, $subject, $bodyText, $textPart, $addressId);
    }
    private function _sendMultiPartMessageFromUs($recipients, $subject, $bodyText, $textPart, $addressId='support' ) {
        $message = $this->_createMessageFromUs($addressId, $subject, $bodyText, 'html');
        $message->setAlternateBodyText($textPart);
        $message->setRecipient($recipients);
        return $this->mailer->send($message);
    }

    public static function disableSend() {
        self::getInstance()->mailer->setSendEnabled(false);
    }

    public static function GetMailboxManager() {
        return self::getInstance()->mailboxes;
    }


}