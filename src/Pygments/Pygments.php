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

namespace Stati\Pygments;

use Ramsey\Pygments\Pygments as BasePygments;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\InputStream;

class Pygments extends BasePygments
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @var InputStream
     */
    protected $input;

    /**
     * @param null $lexer
     * @param null $formatter
     * @param array $options
     * @return Process
     */
    public function getProcess($lexer = null, $formatter = null, $options = [])
    {
        if ($this->process !== null) {
            return $this->process;
        }

        $process = new Process('pygmentize -s -l '.$lexer.' -f '.$formatter);
        $this->input = new InputStream();
        $process->setInput($this->input);
        $process->start();
//        $process->wait();
        $this->process = $process;
        return $this->process;
    }

//    public function highlight($code, $lexer = null, $formatter = null, $options = [])
//    {
//        $process = $this->getProcess($lexer, $formatter, $options);
//
//        $this->input->write($code);
////        $this->input->close();
//
//        var_dump('getStatus');
//        var_dump($process->getOutput());
//        $process->stop();
//    }
}
