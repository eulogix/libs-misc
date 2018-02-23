<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Email\Event;

use Eulogix\Lib\Email\EmailFetcher;
use PhpMimeMailParser\Parser;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EmailProcessedEvent extends Event
{
    /**
     * @var EmailFetcher
     */
    protected $fetcher;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var int
     */
    protected $messageId;

    /**
     * WidgetEvent constructor.
     * @param EmailFetcher $fetcher
     * @param Parser $parser
     * @param int $messageId
     */
    public function __construct(EmailFetcher $fetcher, Parser $parser, $messageId)
    {
        $this->fetcher = $fetcher;
        $this->parser = $parser;
        $this->messageId = $messageId;
    }

    /**
     * @return EmailFetcher
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

}