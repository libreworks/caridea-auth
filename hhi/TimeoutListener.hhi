<?hh

namespace Caridea\Auth;

class TimeoutListener implements \Caridea\Event\Listener
{
    use \Psr\Log\LoggerAwareTrait;
    
    protected int $timeout = 0;

    protected int $expire = 0;
    
    public function __construct(int $timeout = 1200, int $expire = 86400)
    {
    }
    
    public function notify(\Caridea\Event\Event $event): void
    {
    }
}
