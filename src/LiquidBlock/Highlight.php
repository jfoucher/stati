<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
 */

namespace Stati\LiquidBlock;

use Liquid\AbstractBlock;
use Liquid\Context;

class Highlight extends AbstractBlock
{
    public function render(Context $context)
    {
        $language = trim($this->markup);
        $nodes = $this->getNodelist();
        return $this->pygmentize(implode('', $nodes), $language);
    }

    private function pygmentize($string, $lexer = 'php', $format = 'html') {
        // use proc open to start pygmentize
        $descriptorspec = array (
            array("pipe", "r"), // stdin
            array("pipe", "w"), // stdout
            array("pipe", "w"), // stderr
        );

        $cwd = dirname(__FILE__);
        $env = array();

        $proc = proc_open('pygmentize -l ' . $lexer . ' -f ' . $format,
            $descriptorspec, $pipes, $cwd, $env);

        if(!is_resource($proc)) {
            return false;
        }

        // now write $string to pygmentize's input
        fwrite($pipes[0], $string);
        fclose($pipes[0]);

        // the result should be available on stdout
        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // we don't care about stderr in this example

        // just checking the return val of the cmd
        $return_val = proc_close($proc);
        if($return_val !== 0) {
            return false;
        }

        return $result;
    }
}