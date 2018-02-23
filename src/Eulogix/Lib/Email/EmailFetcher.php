<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Email;

use Eulogix\Lib\Email\Event\EmailProcessedEvent;
use Eulogix\Lib\Traits\DispatcherHolder;
use PhpMimeMailParser\Parser;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EmailFetcher {

    use DispatcherHolder;

    const EVENT_EMAIL_PROCESSED = 'EMAIL_PROCESSED';

    /**
     * @var resource
     */
    protected $mailbox;

    /**
     * @param string $connectionString
     * @param string $user
     * @param string $pass
     * @param array $options
     * @throws \Exception
     */
    private function __construct($connectionString, $user, $pass, array $options = []) {
        $this->mailbox = imap_open($connectionString, $user, $pass);
        if(!$this->mailbox)
            throw new \Exception(imap_last_error());
    }

    public function __destruct() {
        $this->commit();
        imap_close($this->mailbox);
    }

    public function commit() {
        imap_expunge($this->mailbox);
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $mailbox
     * @param string $user
     * @param string $pass
     * @return EmailFetcher
     * @throws \Exception
     */
    public static function getIMAPFetcher($host, $port = 143, $mailbox = 'INBOX', $user, $pass) {
        return new self("{{$host}:{$port}}{$mailbox}", $user, $pass);
    }

    /**
     * @return int
     */
    public function getMessagesNumber(){
        return imap_num_msg($this->mailbox);
    }

    /**
     * @param int|null $limit
     * @return $this;
     */
    public function processMessages($limit = null) {
        $messagesNr = $this->getMessagesNumber();
        $maxMessagesToProcess = min($limit ?? $messagesNr, $messagesNr);
        for ($i = 1; $i <= $maxMessagesToProcess; $i++) {
            $fullEmailSource = imap_fetchbody($this->mailbox, $i, "");
            $parser = new Parser();
            $parser->setText($fullEmailSource);
            $this->getDispatcher()->dispatch( self::EVENT_EMAIL_PROCESSED, new EmailProcessedEvent($this, $parser, $i) );
        }
        return $this;
    }

    public function removeMessage($messageId)
    {
        imap_delete($this->mailbox, $messageId);
    }

}