<?php
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
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Auth;

/**
 * A listener which will log out a session if it's idle or has been active for too long.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class TimeoutListener implements \Caridea\Event\Listener
{
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
     * @param int $timeout The number of seconds until a session should be considered idle. If omitted, the default is 20 minutes.
     * @param int $expire The number of seconds until a session should be considered expired. If omitted, the default is 24 hours.
     */
    public function __construct($timeout = 1200, $expire = 86400)
    {
        $this->timeout = (int)$timeout;
        $this->expire = (int)$expire;
    }
    
    public function notify(\Caridea\Event\Event $event)
    {
        if ($event instanceof Event\Resume) {
            $now = microtime(true);
            if (($this->timeout + $event->getLastActive()) < $now) {
                $event->getSource()->logout();
            } else if (($this->expire + $event->getFirstActive()) < $now) {
                $event->getSource()->logout();
            }
        }
    }
}