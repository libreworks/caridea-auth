<?hh // strict

namespace Caridea\Auth\Event;

class Resume extends \Caridea\Auth\Event
{
    protected float $firstActive = 0.0;

    protected float $lastActive = 0.0;

    public function __construct(\Caridea\Auth\Service $source, \Caridea\Auth\Principal $principal, float $firstActive, float $lastActive)
    {
        parent::__construct($source, $principal);
    }
    
    public function getFirstActive(): float
    {
        return 0.0;
    }

    /**
     * Gets the authenticated most recent active time.
     *
     * @return float The authenticated most recent active time
     */
    public function getLastActive(): float
    {
        return 0.0;
    }
}
