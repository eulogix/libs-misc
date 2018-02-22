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

use Eulogix\Lib\Traits\DispatcherHolder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EmailFetcher {

    use DispatcherHolder;

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
     */
    public function processMessages($limit = null) {
        $messagesNr = $this->getMessagesNumber();
        $maxMessagesToProcess = min($limit ?? $messagesNr, $messagesNr);
        for ($i = 1; $i <= $maxMessagesToProcess; $i++) {
            $headers = imap_fetchheader($this->mailbox, $i, FT_PREFETCHTEXT);
            $body = imap_body($this->mailbox, $i);
            //TODO
        }
    }
}