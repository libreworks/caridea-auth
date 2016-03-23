<?hh // strict

namespace Caridea\Auth\Exception;

class UsernameAmbiguous extends \UnexpectedValueException implements \Caridea\Auth\Exception
{
    public function __construct(string $username, ?\Exception $previous = null)
    {
        parent::__construct("There are multiple accounts with the username: $username", 0, $previous);
    }

    public function getUsername(): string
    {
        return '';
    }
}
