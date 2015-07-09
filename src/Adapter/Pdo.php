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
 * PDO authentication adapter.
 *
 * @copyright 2015 LibreWorks contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache 2.0 License
 */
class Pdo extends AbstractAdapter
{
    /**
     * @var \PDO The database connection
     */
    protected $pdo;
    /**
     * @var string The document field containing the username
     */
    protected $fieldUser;
    /**
     * @var string The document field containing the password
     */
    protected $fieldPass;
    /**
     * @var string The table (and possible JOINs) from which to SELECT
     */
    protected $table;
    /**
     * @var string Any additional WHERE parameters
     */
    protected $where;
    
    /**
     * Creates a new PDO authentication adapter.
     *
     * @param \PDO $pdo The PDO driver
     * @param string $fieldUser The document field containing the username
     * @param string $fieldPass The document field containing the hashed password
     * @param string $table The table (and possible JOINs) from which to SELECT
     * @param string $where Any additional WHERE parameters (e.g. "foo = 'bar'")
     */
    public function __construct(\PDO $pdo, $fieldUser, $fieldPass, $table, $where = '')
    {
        $this->pdo = $pdo;
        $this->fieldUser = $this->checkBlank($fieldUser, "username");
        $this->fieldPass = $this->checkBlank($fieldPass, "password");
        $this->table = $this->checkBlank($table, "table");
        $this->where = $where;
    }
    
    /**
     * Authenticates the current principal using the provided credentials.
     *
     * This method expects two request body values to be available. These are
     * `username` and `password`, as provided by the authenticating user.
     *
     * The principal details will include `ip` (remote IP address), and `ua`
     * (remote User Agent).
     *
     * @param ServerRequestInterface $request The Server Request message containing credentials
     * @return \Caridea\Auth\Principal An authenticated principal
     * @throws \Caridea\Auth\Exception\MissingCredentials If the username or password is empty
     * @throws \Caridea\Auth\Exception\UsernameNotFound if the provided username wasn't found
     * @throws \Caridea\Auth\Exception\UsernameAmbiguous if the provided username matches multiple accounts
     * @throws \Caridea\Auth\Exception\InvalidPassword if the provided password is invalid
     * @throws \Caridea\Auth\Exception\ConnectionFailed if a PDO error is encountered
     */
    public function login(ServerRequestInterface $request)
    {
        $post = $request->getParsedBody();
        $username = $this->ensure($post, 'username');
        try {
            $stmt = $this->execute($username, $request);
            $row = $this->fetchResult($stmt->fetchAll(\PDO::FETCH_NUM), $username);
            $this->verify($this->ensure($post, 'password'), $row[1]);
            return \Caridea\Auth\Principal::get(
                $username,
                $this->details($request, [])
            );
        } catch (\PDOException $e) {
            throw new \Caridea\Auth\Exception\ConnectionFailed($e);
        }
    }
    
    /**
     * Queries the database table.
     *
     * Override this method if you want to bind additonal values to the SQL
     * query.
     *
     * @param string $username The username to use for parameter binding
     * @param ServerRequestInterface $request The Server Request message (to use for additional parameter binding)
     */
    protected function execute($username, ServerRequestInterface $request)
    {
        $stmt = $this->pdo->prepare($this->getSql());
        $stmt->execute([$username]);
        return $stmt;
    }
    
    /**
     * Builds the SQL query to be executed.
     *
     * @return string The SQL query
     */
    protected function getSql()
    {
        $sql = "SELECT {$this->fieldUser}, {$this->fieldPass} FROM {$this->table} WHERE {$this->fieldUser} = ?";
        if ($this->where) {
            $sql .= " AND ({$this->where})";
        }
        return $sql;
    }
    
    /**
     * Fetches a single result from the database resultset.
     *
     * @param array $results The results as returned from `fetchAll`
     * @param string $username The attempted username (for Exception purposes)
     * @return array A single database result
     * @throws \Caridea\Auth\Exception\UsernameAmbiguous If there is more than 1 result
     * @throws \Caridea\Auth\Exception\UsernameNotFound If there are 0 results
     */
    protected function fetchResult(array $results, $username)
    {
        if (count($results) > 1) {
            throw new \Caridea\Auth\Exception\UsernameAmbiguous($username);
        } elseif (count($results) == 0) {
            throw new \Caridea\Auth\Exception\UsernameNotFound($username);
        }
        return current($results);
    }
}
