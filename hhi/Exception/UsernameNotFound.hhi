<?hh // strict

namespace Caridea\Auth\Exception;

class UsernameNotFound extends \InvalidArgumentException implements \Caridea\Auth\Exception
{
    public function __construct(string $username, ?\Exception $previous = null)
    {
        parent::__construct("Username not found: $username", 0, $previous);
    }
    
    public function getUsername(): string
    {
        return '';
    }
}
