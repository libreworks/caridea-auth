<?hh // strict

namespace Caridea\Auth\Exception;

class InvalidPassword extends \InvalidArgumentException implements \Caridea\Auth\Exception
{
    public function __construct(?\Exception $previous = null)
    {
        parent::__construct("Invalid password", 0, $previous);
    }
}
