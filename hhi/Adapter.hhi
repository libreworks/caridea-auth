<?hh

namespace Caridea\Auth;

interface Adapter
{
    public function login(\Psr\Http\Message\ServerRequestInterface $request): Principal;
}
