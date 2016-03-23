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
namespace Caridea\Auth\Event;

/**
 * An authentication resume event. This will only be published for non-anonymous principals.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Resume extends \Caridea\Auth\Event
{
    /**
     * @var float The authenticated first active time
     */
    protected $firstActive;
    /**
     * @var float The authenticated most recent active time
     */
    protected $lastActive;
    
    /**
     * Creates a new resume event.
     *
     * @param \Caridea\Auth\Service $source The source of the event. Cannot be null.
     * @param \Caridea\Auth\Principal $principal The authenticated principal
     * @param float $firstActive The authenticated first active time
     * @param float $lastActive The authenticated most recent active time
     */
    public function __construct(\Caridea\Auth\Service $source, \Caridea\Auth\Principal $principal, float $firstActive, float $lastActive)
    {
        parent::__construct($source, $principal);
        $this->firstActive = $firstActive;
        $this->lastActive = $lastActive;
    }
    
    /**
     * Gets the authenticated first active time.
     *
     * @return float The authenticated first active time
     */
    public function getFirstActive(): float
    {
        return $this->firstActive;
    }

    /**
     * Gets the authenticated most recent active time.
     *
     * @return float The authenticated most recent active time
     */
    public function getLastActive(): float
    {
        return $this->lastActive;
    }
}
