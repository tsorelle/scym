<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/5/2015
 * Time: 5:57 PM
 */

namespace App\services\mailboxes;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TEMailMessage;
use Tops\sys\TPostOffice;

class SendMessageCommand extends TServiceCommand {
    
    
    private function createMessage($dto) {
        if (!$dto) {
            $this->addErrorMessage('No service request recieved.');
            return null;
        }

        $toName =  isset($dto->toName) ? trim($dto->toName) : '';
        $toAddress =  isset($dto->toAddress) ? trim($dto->toAddress) : '';
        $fromName =  isset($dto->fromName) ? trim($dto->fromName) : '';
        $fromAddress =  isset($dto->fromAddress) ? trim($dto->fromAddress) : '';
        $subject =  isset($dto->subject) ? trim($dto->subject) : '';
        $body =  isset($dto->body) ? trim($dto->body) : '';

        $message = new TEMailMessage();
        if (empty($toAddress)) {
            $this->addErrorMessage('An e-mail address for the recipient is required');
        }
        else {
            $toName =  empty($toName) ? null : trim($toName);
            $message->setRecipient($toAddress,$toName);
        }

        if (empty($fromAddress)) {
            $this->addErrorMessage('Your e-mail address is required');
        }
        else {
            $fromName =  empty($fromName) ? null : trim($fromName);
            $message->setFromAddress($fromAddress,$fromName);
        }

        if (empty($subject)) {
            $this->addErrorMessage('A subject is required');
        }
        else {
            if (stristr( $subject,'http:') || stristr($subject,'https:')) {
                $this->addErrorMessage('Links or urls are not permitted in these messages.');
            }
            else {
                $message->setSubject($subject);
            }
        }

        if (empty($body)) {
            $this->addErrorMessage('A message body is required');
        }
        else {
            $text =  strtolower($body);
            if (strstr( $text,'http:') || strstr( $text,'https:')) {
                $this->addErrorMessage('Links or urls are not permitted in these messages.');
            }
            else {
                $message->setMessageBody($body);
            }
        }

        $errors = $message->getValidationErrors();
        foreach($errors as $email => $error) {
            $this->addErrorMessage("The address '$email' is invalid: $error");
        }
        return $message;
    }
    
    protected function run()
    {
        $dto = $this->getRequest();
        $message = $this->createMessage($dto);
        if ($message == null || $this->hasErrors()) {
            $this->addErrorMessage('Sorry, we are not able to send your message.');
            return;
        }
        TPostOffice::Send($message);
        $this->addInfoMessage("Message sent.");

    }
}