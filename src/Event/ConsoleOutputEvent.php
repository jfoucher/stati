<?php
/**
 * ConsoleOutputEvent.php
 *
 * Created By: jonathan
 * Date: 28/09/2017
 * Time: 22:06
 */

namespace Stati\Event;


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

    public function __construct($method, $args)
    {
        $this->method = $method;
        $this->args = $args;
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


}