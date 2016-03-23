<?hh // strict

namespace Caridea\Auth\Exception;

class ConnectionFailed extends \RuntimeException implements \Caridea\Auth\Exception
{
    public function __construct(?\Exception $previous = null)
    {
        parent::__construct("Cannot read from source data", 0, $previous);
    }
}
