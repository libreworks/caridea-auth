<?hh

namespace Caridea\Auth\Adapter;

use \Psr\Http\Message\ServerRequestInterface;

class MongoDb extends AbstractAdapter
{
    protected \MongoDB\Driver\Manager $manager;

    protected string $collection = '';

    protected ?\MongoDB\Driver\ReadPreference $rp;

    protected string $fieldUser = '';

    protected string $fieldPass = '';

    protected array<string,mixed> $query = [];

    public function __construct(\MongoDB\Driver\Manager $manager, string $collection, string $fieldUser, string $fieldPass, array<string,mixed> $query = [], ?\MongoDB\Driver\ReadPreference $rp = null)
    {
        $this->manager = $manager;
        $this->rp = $rp;
    }    
    
    public function login(ServerRequestInterface $request): \Caridea\Auth\Principal
    {
        return \Caridea\Auth\Principal::getAnonymous();
    }
    
    protected function getResults(string $username, ServerRequestInterface $request): \MongoDB\Driver\Cursor
    {
        $q = new \MongoDB\Driver\Query([]);
        return $this->manager->executeQuery($this->collection, $q, $this->rp);
    }

    protected function fetchResult(\MongoDB\Driver\Cursor $results, string $username): \stdClass
    {
        return new \stdClass();
    }
}
