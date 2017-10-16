<?php

/*
 * This file is part of the Stati package.
 *
 * Copyright 2017 Jonathan Foucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package Stati
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
    public function getMinLevel()
    {
        return $this->minLevel;
    }
}
