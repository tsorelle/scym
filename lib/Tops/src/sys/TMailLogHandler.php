<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/28/2015
 * Time: 12:04 PM
 */

namespace Tops\sys;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Class TMailLogHandler
 * @package Tops\sys
 */
class TMailLogHandler extends AbstractProcessingHandler  {

    /**
     * @var IMailer
     */
    private $mailer;
    /**
     * @var TEMailMessage
     */
    private $message;

    private $startTime;
    private $messageCount;
    const maxMessages = 20;


    public function __construct(IMailer $mailer, $sender, $recipient, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->message = new TEMailMessage();
        $this->message->setRecipient($recipient);
        $this->message->setFromAddress($sender);
        $this->message->setSubject( "Message logged on ".date(DATE_RFC850));

        $this->startTime = time();
        $this->messageCount = 0;

        $this->mailer = $mailer;
    }

    public function stop() {
        $this->mailer->setSendEnabled(false);
    }


    /**
     * Send a mail with the given content
     *
     * @param string $content
     * @param array $records the array of log records that formed this content
     */
    protected function send($content, array $records)
    {

        if (!empty($records)) {
            $count= sizeof($records);
            if ($count == 1) {
                $content = "One error was logged.\n\n" . $content;
            }
            else {
                $content = $count." errors were logged.\n\n".$content;;
            }
        }
        $message = clone $this->message;
        $message->setMessageText($content);
        $message-> setTimeStamp(time());
        $this->mailer->send($message);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        // Throttle messages to 20 an hour to prevent runaway spam.
        $currentTime = time();
        $elapsed = $this->startTime - $currentTime;
        if ($elapsed > 3600) {
            $this->startTime = $currentTime;
            $this->messageCount = 0;
        }
        if ($this->messageCount < self::maxMessages) {
            $this->send((string) $record['formatted'], array($record));
        }
    }
}