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
 * Security principal; an authenticated or anonymous user.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Principal
{
    /**
     * @var string The principal username
     */
    protected $username;
    /**
     * @var bool Whether the principal is anonymous
     */
    protected $anonymous;
    /**
     * @var array Associative array of extra details
     */
    protected $details;
    
    /**
     * @var Principal singleton instance of the anonymous Principal
     */
    private static $anon;
    
    /**
     * Creates a new security principal.
     *
     * @param string $username
     * @param array $details
     * @param boolean $anonymous
     */
    protected function __construct($username, array $details, $anonymous = false)
    {
        $this->username = $username;
        $this->details = $details;
        $this->anonymous = $anonymous;
    }
    
    /**
     * Gets a key-value array containing any authentication details.
     *
     * @return array The auth details
     */
    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * Gets the authenticated principal username.
     *
     * An anonymous user has a `null` username.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Gets whether this authentication is anonymous.
     *
     * @return bool Whether this principal is anonymous
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }
    
    /**
     * Gets a string representation.
     *
     * @return string The string representation
     */
    public function __toString()
    {
        return $this->anonymous ? "[anonymous]" : $this->username;
    }
    
    /**
     * Gets a non-anonymous Principal.
     *
     * @param string $username The principal username
     * @param array $details Any authentication details
     * @return Principal The principal
     */
    public static function get($username, array $details)
    {
        return new self($username, $details);
    }
    
    /**
     * Gets a token representing an anonymous authentication.
     *
     * @return Principal The anonymous principal
     */
    public static function getAnonymous()
    {
        if (self::$anon === null) {
            self::$anon = new self(null, [], true);
        }
        return self::$anon;
    }
}
