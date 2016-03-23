<?hh

namespace Caridea\Auth\Adapter;

class Cert extends AbstractAdapter
{
    protected string $name = '';

    protected ?string $regex;

    public function __construct(string $name = 'SSL_CLIENT_S_DN', ?string $regex = null)
    {
    }

    public function login(\Psr\Http\Message\ServerRequestInterface $request): \Caridea\Auth\Principal
    {
        return \Caridea\Auth\Principal::getAnonymous();
    }
}
