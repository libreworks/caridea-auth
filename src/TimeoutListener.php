<?php
declare(strict_types=1);
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Auth;

/**
 * A listener which will log out a session if it's idle or has been active for too long.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class TimeoutListener implements \Caridea\Event\Listener
{
    use \Psr\Log\LoggerAwareTrait;
    
    /**
     * @var int Session timeout length in seconds
     */
    protected $timeout;
    /**
     * @var int Session expiration length in seconds
     */
    protected $expire;
    
    /**
     * Creates a new timeout listener.
     *
     * @param int $timeout The number of seconds until a session should be
     *      considered idle. If omitted, the default is 20 minutes.
     * @param int $expire The number of seconds until a session should be
     *      considered expired. If omitted, the default is 24 hours.
     */
    public function __construct(int $timeout = 1200, int $expire = 86400)
    {
        $this->timeout = (int)$timeout;
        $this->expire = (int)$expire;
        $this->logger = new \Psr\Log\NullLogger();
    }
    
    /**
     * Notifies this object that an event has occurred.
     *
     * @param \Caridea\Event\Event $event The incoming event
     */
    public function notify(\Caridea\Event\Event $event)
    {
        if ($event instanceof Event\Resume) {
            $now = microtime(true);
            if (($this->timeout + $event->getLastActive()) < $now) {
                $this->logger->info(
                    "Authentication for {user} has timed out",
                    ['user' => $event->getPrincipal()]
                );
                $event->getSource()->logout();
            } elseif (($this->expire + $event->getFirstActive()) < $now) {
                $this->logger->info(
                    "Authentication for {user} has expired",
                    ['user' => $event->getPrincipal()]
                );
                $event->getSource()->logout();
            }
        }
    }
}
