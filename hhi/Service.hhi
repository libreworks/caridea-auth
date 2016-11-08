<?hh

namespace Caridea\Auth;

use Caridea\Event\Publisher;
use Caridea\Session\Session;
use Caridea\Session\Map as Smap;
use Psr\Http\Message\ServerRequestInterface;

class Service implements \Caridea\Event\PublisherAware
{
    use \Psr\Log\LoggerAwareTrait;
    use \Caridea\Event\PublisherSetter;

    protected ?Adapter $adapter;

    protected Session $session;

    protected Smap $values;

    protected ?Principal $principal;

    public function __construct(Session $session, ?Publisher $publisher = null, ?Adapter $adapter = null)
    {
        $this->session = $session;
        $this->values = $session->getValues(__CLASS__);
        $this->publisher = $publisher ?? new \Caridea\Event\NullPublisher();
    }

    public function getPrincipal(): Principal
    {
        return Principal::getAnonymous();
    }

    public function login(ServerRequestInterface $request, ?Adapter $adapter = null): bool
    {
        return false;
    }

    protected function publishLogin(Principal $principal): bool
    {
        return true;
    }

    public function resume(): bool
    {
        return false;
    }

    protected function publishResume(Principal $principal, Smap $values): void
    {
    }

    public function logout(): bool
    {
        return false;
    }

    protected function publishLogout(Principal $principal): bool
    {
        return false;
    }
}
