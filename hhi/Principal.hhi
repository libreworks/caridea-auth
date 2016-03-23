<?hh // strict

namespace Caridea\Auth;

class Principal
{
    protected ?string $username;

    protected bool $anonymous;

    protected array<string,mixed> $details;
    
    protected function __construct(?string $username = null, array<string,mixed> $details = [], bool $anonymous = false)
    {
        $this->username = $username;
        $this->details = $details;
        $this->anonymous = $anonymous;
    }

    public function getDetails(): array<string,mixed>
    {
        return [];
    }

    public function getUsername(): ?string
    {
        return '';
    }

    public function isAnonymous(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return '';
    }

    public static function get(string $username, array<string,mixed> $details): Principal
    {
        return new self($username, $details);
    }

    public static function getAnonymous(): Principal
    {
        return new Principal('', []);
    }
}
