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
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Auth;

/**
 * An authentication event. These should only be published for non-anonymous principals.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
abstract class Event extends \Caridea\Event\Event
{
    /**
     * @var Principal the authenticated principal
     */
    protected $principal;
    
    /**
     * Creates a new Authentication Event.
     *
     * @param Service $source The source of the event. Cannot be null.
     * @param Principal $principal The authenticated principal
     */
    public function __construct(Service $source, Principal $principal)
    {
        parent::__construct($source);
        $this->principal = $principal;
    }
    
    /**
     * Gets the authenticated principal
     *
     * @return Principal The authenticated principal
     */
    public function getPrincipal(): Principal
    {
        return $this->principal;
    }
}
