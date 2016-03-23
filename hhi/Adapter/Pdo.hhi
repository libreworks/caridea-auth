<?hh

namespace Caridea\Auth\Adapter;

use \Psr\Http\Message\ServerRequestInterface;

class Pdo extends AbstractAdapter
{
    protected \PDO $pdo;

    protected string $fieldUser = '';

    protected string $fieldPass = '';

    protected string $table = '';

    protected string $where = '';
    
    public function __construct(\PDO $pdo, string $fieldUser, string $fieldPass, string $table, string $where = '')
    {
        $this->pdo = $pdo;
    }
    
    public function login(ServerRequestInterface $request): \Caridea\Auth\Principal
    {
        return \Caridea\Auth\Principal::getAnonymous();
    }
    
    protected function execute(string $username, ServerRequestInterface $request): \PDOStatement
    {
        $stmt = $this->pdo->prepare('');
        $stmt->execute([$username]);
        return $stmt;
    }

    protected function getSql(): string
    {
        return '';
    }

    protected function fetchResult(array<array<mixed>> $results, string $username): array<mixed>
    {
        return [];
    }
}
