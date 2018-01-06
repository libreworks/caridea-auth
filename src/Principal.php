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
 * Security principal; an authenticated or anonymous user.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
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
     * @param string|null $username
     * @param array $details
     * @param bool $anonymous
     */
    protected function __construct(?string $username = null, array $details = [], bool $anonymous = false)
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
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Gets the authenticated principal username.
     *
     * An anonymous user has a `null` username.
     *
     * @return string|null The username, or `null`
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Gets whether this authentication is anonymous.
     *
     * @return bool Whether this principal is anonymous
     */
    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    /**
     * Gets a string representation.
     *
     * @return string The string representation
     */
    public function __toString(): string
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
    public static function get(string $username, array $details): Principal
    {
        return new self($username, $details);
    }

    /**
     * Gets a token representing an anonymous authentication.
     *
     * @return Principal The anonymous principal
     */
    public static function getAnonymous(): Principal
    {
        if (self::$anon === null) {
            self::$anon = new self(null, [], true);
        }
        return self::$anon;
    }
}
