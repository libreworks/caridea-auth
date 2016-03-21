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
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
namespace Caridea\Auth\Adapter;

/**
 * Client SSL certificate authentication adapter.
 *
 * @copyright 2015-2016 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Cert extends AbstractAdapter
{
    /**
     * @var string The `$_SERVER` key which contains the DN
     */
    protected $name;
    /**
     * @var string Regex to match username from DN
     */
    protected $regex;
    
    /**
     * Creates a new client certificate authentication adapter.
     *
     * ```php
     * // $_SERVER['SSL_CLIENT_S_DN'] = '/O=Acme, Inc/CN=Bob';
     * $adapter = new Cert();
     * $adapter->login($request); // username: "/O=Acme, Inc/CN=Bob"
     *
     * // $_SERVER['DN'] = '/O=Acme, Inc/CN=Bob';
     * $adapter = new Cert('DN', '#CN=(.+)$#');
     * $adapter->login($request); // username: "Bob"
     * ```
     *
     * @param string $name The `$_SERVER` key which contains the user cert DN
     * @param string $regex A regex to match a username inside the DN (if not
     *     specified, username is the entire DN). Must have one capture pattern.
     */
    public function __construct($name = 'SSL_CLIENT_S_DN', $regex = null)
    {
        $this->name = $this->checkBlank($name, "name");
        $this->regex = $regex;
    }
    
    /**
     * Authenticates the current principal using the provided credentials.
     *
     * This method will retrieve a value from the SERVER attributes in the
     * offset at `$this->name`.
     *
     * The principal details will include `ip` (remote IP address), `ua` (remote
     * User Agent), and `dn` (client SSL distinguished name).
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The Server Request message containing credentials
     * @return \Caridea\Auth\Principal An authenticated principal
     * @throws \Caridea\Auth\Exception\MissingCredentials if no value was found
     *     in the SERVER field or the provided regular expression doesn't match
     */
    public function login(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $server = $request->getServerParams();
        $username = $dn = $this->ensure($server, $this->name);
        if ($this->regex) {
            if (preg_match($this->regex, $dn, $matches)) {
                $username = $matches[1];
            } else {
                throw new \Caridea\Auth\Exception\MissingCredentials();
            }
        }
        return \Caridea\Auth\Principal::get(
            $username,
            $this->details($request, ['dn' => $dn])
        );
    }
}
