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

use Caridea\Event\Publisher;
use Caridea\Session\Session;
use Caridea\Session\Map;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Authentication service.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Service implements \Caridea\Event\PublisherAware, \Psr\Log\LoggerAwareInterface
{
    use \Psr\Log\LoggerAwareTrait;
    use \Caridea\Event\PublisherSetter;

    /**
     * @var Adapter The default auth adapter
     */
    protected $adapter;
    /**
     * @var \Caridea\Session\Session The session utility
     */
    protected $session;
    /**
     * @var \Caridea\Session\Map The session values
     */
    protected $values;
    /**
     * @var Principal The authenticated principal
     */
    protected $principal;

    /**
     * Creates a new authentication service.
     *
     * @param Session $session The session utility
     * @param Publisher|null $publisher An event publisher to broadcast authentication events
     * @param Adapter|null $adapter A default authentication adapter
     */
    public function __construct(Session $session, ?Publisher $publisher = null, ?Adapter $adapter = null)
    {
        $this->session = $session;
        $this->values = $session->getValues(__CLASS__);
        $this->publisher = $publisher ?? new \Caridea\Event\NullPublisher();
        $this->adapter = $adapter;
        $this->logger = new \Psr\Log\NullLogger();
    }

    /**
     * Gets the currently authenticated principal.
     *
     * If no one is authenticated, this will return an anonymous Principal. If
     * The session is not started but can be resumed, it will be resumed and the
     * principal will be loaded.
     *
     * @return Principal the authenticated principal
     */
    public function getPrincipal(): Principal
    {
        if ($this->principal === null && !$this->resume()) {
            $this->principal = Principal::getAnonymous();
        }
        return $this->principal;
    }

    /**
     * Authenticates a principal.
     *
     * @param ServerRequestInterface $request The Server Request message containing credentials
     * @param Adapter|null $adapter An optional adapter to use.
     *     Will use the default authentication adapter if none is specified.
     * @return bool Whether the session could be established
     * @throws \InvalidArgumentException If no adapter is provided and no default adapter is set
     * @throws Exception\UsernameNotFound if the provided username wasn't found
     * @throws Exception\UsernameAmbiguous if the provided username matches multiple accounts
     * @throws Exception\InvalidPassword if the provided password is invalid
     * @throws Exception\ConnectionFailed if the access to a remote data source failed
     *     (e.g. missing flat file, unreachable LDAP server, database login denied)
     */
    public function login(ServerRequestInterface $request, ?Adapter $adapter = null): bool
    {
        $started = $this->session->resume() || $this->session->start();
        if (!$started) {
            return false;
        }

        $login = $adapter ?? $this->adapter;
        if ($login === null) {
            throw new \InvalidArgumentException('You must specify an adapter for authentication');
        }
        $this->principal = $principal = $login->login($request);

        $this->session->clear();
        $this->session->regenerateId();

        $this->values->offsetSet('principal', $principal);
        $now = microtime(true);
        $this->values->offsetSet('firstActive', $now);
        $this->values->offsetSet('lastActive', $now);

        $this->logger->info(
            "Authentication login: {user}",
            ['user' => $principal]
        );
        return $this->publishLogin($principal);
    }

    /**
     * Publishes the login event.
     *
     * @param \Caridea\Auth\Principal $principal The authenticated principal
     * @return bool Always true
     */
    protected function publishLogin(Principal $principal): bool
    {
        $this->publisher->publish(new Event\Login($this, $principal));
        return true;
    }

    /**
     * Resumes an existing authenticated session.
     *
     * @return bool If an authentication session existed
     */
    public function resume(): bool
    {
        if ($this->values->offsetExists('principal')) {
            $this->principal = $this->values->get('principal');

            $this->logger->info(
                "Authentication resume: {user}",
                ['user' => $this->principal]
            );
            $this->publishResume($this->principal, $this->values);

            $this->values->offsetSet('lastActive', microtime(true));

            return true;
        }
        return false;
    }

    /**
     * Publishes the resume event.
     *
     * @param \Caridea\Auth\Principal $principal The authenticated principal
     * @param \Caridea\Session\Map $values The session values
     */
    protected function publishResume(Principal $principal, Map $values)
    {
        $this->publisher->publish(new Event\Resume(
            $this,
            $principal,
            $values->get('firstActive') ?? 0.0,
            $values->get('lastActive') ?? 0.0
        ));
    }

    /**
     * Logs out the currently authenticated principal.
     *
     * @return bool If a principal existed in the session to log out
     */
    public function logout(): bool
    {
        if ($this->values->offsetExists('principal')) {
            $principal = $this->getPrincipal();
            $this->principal = Principal::getAnonymous();

            $this->session->destroy();

            $this->logger->info(
                "Authentication logout: {user}",
                ['user' => $principal]
            );
            return $this->publishLogout($principal);
        }
        return false;
    }

    /**
     * Publishes the logout event.
     *
     * @param \Caridea\Auth\Principal $principal The authenticated principal
     * @return bool Always true
     */
    protected function publishLogout(Principal $principal): bool
    {
        $this->publisher->publish(new Event\Logout($this, $principal));
        return true;
    }
}
