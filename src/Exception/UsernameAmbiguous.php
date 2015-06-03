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
namespace Caridea\Auth\Exception;

/**
 * Exception for when a provided username matches multiple accounts.
 * 
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class UsernameAmbiguous extends \Caridea\Auth\Exception
{
    /**
     * @var string The username
     */
    private $username;
    
    /**
     * Creates a new exception.
     * 
     * @param string $username The ambiguous username
     * @param Exception $previous Optional preceding exception
     */
    public function __construct($username, \Exception $previous = null)
    {
        parent::__construct("There are multiple accounts with the username: $username", 0, $previous);
        $this->username = $username;
    }
    
    /**
     * Gets the username.
     * 
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }
}