<?php
/**
 * Pygments.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 16:31
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