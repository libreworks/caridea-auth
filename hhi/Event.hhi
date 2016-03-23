<?hh // strict

namespace Caridea\Auth;

abstract class Event extends \Caridea\Event\Event<\Caridea\Auth\Service>
{
    protected Principal $principal;

    public function __construct(Service $source, Principal $principal)
    {
        parent::__construct($source);
        $this->principal = $principal;
    }

    public function getPrincipal(): Principal
    {
        return $this->principal;
    }
}
