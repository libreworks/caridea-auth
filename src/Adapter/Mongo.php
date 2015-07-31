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
namespace Caridea\Auth\Adapter;

use \Psr\Http\Message\ServerRequestInterface;

/**
 * MongoDB authentication adapter.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Mongo extends AbstractAdapter
{
    /**
     * @var \MongoCollection The MongoDB document collection of user accounts
     */
    protected $collection;
    /**
     * @var string The document field containing the username
     */
    protected $fieldUser;
    /**
     * @var string The document field containing the password
     */
    protected $fieldPass;
    /**
     * @var array Associative array to use to limit user accounts
     */
    protected $query = [];
    
    /**
     * Creates a new MongoDB authentication adapter.
     *
     * @param \MongoCollection $collection The MongoDB collection of user accounts
     * @param string $fieldUser The document field containing the username
     * @param string $fieldPass The document field containing the hashed password
     * @param array $query Optional associative array to use to limit user accounts
     */
    public function __construct(\MongoCollection $collection, $fieldUser, $fieldPass, $query = [])
    {
        $this->collection = $collection;
        $this->fieldUser = $this->checkBlank($fieldUser, "username");
        $this->fieldPass = $this->checkBlank($fieldPass, "password");
        $this->query = $query;
    }
    
    /**
     * Authenticates the current principal using the provided credentials.
     *
     * This method expects two request body values to be available. These are
     * `username` and `password`, as provided by the authenticating user.
     *
     * The principal details will include `ip` (remote IP address), `ua` (remote
     * User Agent), and `ip` (MongoDB document `_id` field for user record).
     *
     * @param ServerRequestInterface $request The Server Request message containing credentials
     * @return \Caridea\Auth\Principal An authenticated principal
     * @throws \Caridea\Auth\Exception\MissingCredentials If the username or password is empty
     * @throws \Caridea\Auth\Exception\UsernameNotFound if the provided username wasn't found
     * @throws \Caridea\Auth\Exception\UsernameAmbiguous if the provided username matches multiple accounts
     * @throws \Caridea\Auth\Exception\InvalidPassword if the provided password is invalid
     * @throws \Caridea\Auth\Exception\ConnectionFailed if a MongoDB error is encountered
     */
    public function login(ServerRequestInterface $request)
    {
        $post = (array) $request->getParsedBody();
        $username = $this->ensure($post, 'username');
        try {
            $results = $this->getResults($username, $request);
            $doc = $this->fetchResult($results, $username);
            $this->verify($this->ensure($post, 'password'), $doc[$this->fieldPass]);
            return \Caridea\Auth\Principal::get(
                $username,
                $this->details($request, ['id' => $doc['_id']])
            );
        } catch (\MongoException $e) {
            throw new \Caridea\Auth\Exception\ConnectionFailed($e);
        }
    }
    
    /**
     * Queries the MongoDB collection.
     *
     * @param string $username The username to use for parameter binding
     * @param ServerRequestInterface $request The Server Request message (to use for additional parameter binding)
     * @return \MongoCursor The results cursor
     */
    protected function getResults($username, ServerRequestInterface $request)
    {
        return $this->collection->find(
            $this->query + [($this->fieldUser) => $username],
            [($this->fieldUser) => true, ($this->fieldPass) => true]
        );
    }

    /**
     * Fetches a single result from the Mongo Cursor.
     *
     * @param \MongoCursor $results The results
     * @param string $username The attempted username (for Exception purposes)
     * @return array A single MongoDB document
     * @throws \Caridea\Auth\Exception\UsernameAmbiguous If there is more than 1 result
     * @throws \Caridea\Auth\Exception\UsernameNotFound If there are 0 results
     */
    protected function fetchResult(\MongoCursor $results, $username)
    {
        if ($results->count() > 1) {
            throw new \Caridea\Auth\Exception\UsernameAmbiguous($username);
        } elseif ($results->count() == 0) {
            throw new \Caridea\Auth\Exception\UsernameNotFound($username);
        }
        return $results->getNext();
    }
}
