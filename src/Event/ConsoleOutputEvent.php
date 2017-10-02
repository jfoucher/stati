<?php
/**
 * ConsoleOutputEvent.php
 *
 * Created By: jonathan
 * Date: 28/09/2017
 * Time: 22:06
 */

namespace Stati\Event;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class ConsoleOutputEvent extends Event
{
    /**
     * Method to call on the SymfonyStyle
     *
     * @var string
     */
    protected $method;

    /**
     * Arguments to pass to the method call
     * @var mixed
     */
    protected $args;

    /**
     * Minimum verbosity level to display message
     * @var int
     */
    protected $minLevel;

    public function __construct($method, $args, $minLevel = OutputInterface::VERBOSITY_VERBOSE)
    {
        $this->method = $method;
        $this->args = $args;
        $this->minLevel = $minLevel;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return int
     */
    public function getMinLevel(): int
    {
        return $this->minLevel;
    }
}
