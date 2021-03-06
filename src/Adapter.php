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
 * Authentication adapter.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
interface Adapter
{
    /**
     * Authenticates the current principal using the provided credentials.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The Server Request message containing credentials
     * @return Principal An authenticated principal
     * @throws Exception\MissingCredentials if any required credentials weren't provided
     * @throws Exception\UsernameNotFound if the provided username wasn't found
     * @throws Exception\UsernameAmbiguous if the provided username matches multiple accounts
     * @throws Exception\InvalidPassword if the provided password is invalid
     * @throws Exception\ConnectionFailed if the access to a remote data source failed
     *     (e.g. missing flat file, unreachable LDAP server, database login denied)
     */
    public function login(\Psr\Http\Message\ServerRequestInterface $request): Principal;
}
