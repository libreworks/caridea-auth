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
namespace Caridea\Auth\Event;

/**
 * An authentication resume event. This will only be published for non-anonymous principals.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Resume extends \Caridea\Auth\Event
{
    /**
     * @var float
     */
    protected $firstActive;
    /**
     * @var float
     */
    protected $lastActive;
    
    /**
     * Creates a new resume event.
     *
     * @param object $source The source of the event. Cannot be null.
     * @param \Caridea\Auth\Principal $principal The authenticated principal
     * @param float $firstActive The authenticated first active time
     * @param float $lastActive The authenticated most recent active time
     */
    public function __construct($source, \Caridea\Auth\Principal $principal, $firstActive, $lastActive)
    {
        parent::__construct($source, $principal);
        $this->firstActive = (float)$firstActive;
        $this->lastActive = (float)$lastActive;
    }
    
    /**
     * @return float The authenticated first active time
     */
    public function getFirstActive()
    {
        return $this->firstActive;
    }

    /**
     * @return float The authenticated most recent active time
     */
    public function getLastActive()
    {
        return $this->lastActive;
    }
}
