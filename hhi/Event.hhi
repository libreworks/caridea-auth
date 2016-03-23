<?hh // strict

namespace Caridea\Auth;

abstract class Event<T> extends \Caridea\Event\Event<T>
{
    protected Principal $principal;

    public function __construct(T $source, Principal $principal)
    {
        parent::__construct($source);
        $this->principal = $principal;
    }

    public function getPrincipal(): Principal
    {
        return $this->principal;
    }
}
