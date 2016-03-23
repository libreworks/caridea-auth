<?hh // strict

namespace Caridea\Auth\Exception;

class MissingCredentials extends \InvalidArgumentException implements \Caridea\Auth\Exception
{
    public function __construct(?\Exception $previous = null)
    {
        parent::__construct("Required credentials were missing", 0, $previous);
    }
}
