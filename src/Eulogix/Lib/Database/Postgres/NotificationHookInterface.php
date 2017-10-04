<?php
/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Eulogix\Lib\Database\Postgres;

interface NotificationHookInterface
{
    /**
     * @param NotificationEvent $e
     * @return bool
     */
    public function mustExecute(NotificationEvent $e);

    /**
     * @param NotificationEvent $e
     */
    public function execute(NotificationEvent $e);
}