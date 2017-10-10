<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Parser;

use ParsedownExtra;

class MarkdownParser extends ParsedownExtra
{
    protected function paragraph($Line)
    {
        // In paragraph first line, check if it's a class
        $Block = array(
            'element' => array(
                'name' => 'p',
                'text' => $Line['text'],
                'handler' => 'line',
            ),
        );

        if (preg_match('/^\{\:(.+)\}/', $Line['text'], $matches)) {
            $item = trim($matches[1]);
            if (strpos($item, '.') === 0) {
                //Is a class
                $Block['element']['attributes'] = ['class' => substr($item, 1)];
                $Block['element']['text'] = '';
            }
            if (strpos($item, '#') === 0) {
                //Is an id
                $Block['element']['attributes'] = ['id' => substr($item, 1)];
                $Block['element']['text'] = '';
            }
        }

        return $Block;
    }

    /**
     * This variant checks the last line of a paragraph to add class or id if necessary
     * @param array $Element
     * @return string
     */
    protected function element(array $Element)
    {
        $markup = '<'.$Element['name'];

        if ($Element['name'] === 'p' && isset($Element['text']) && strpos($Element['text'], '{') !== false) {
            $lines = explode("\n", $Element['text']);
            $lastLine = array_pop($lines);
            if (preg_match('/^\{\:(.+)\}/', $lastLine, $matches)) {
                $item = trim($matches[1]);
                
                if (strpos($item, '.') === 0) {
                    //Is a class
                    $Element['attributes'] = ['class' => substr($item, 1)];
                    $Element['text'] = implode("\n", $lines);
                }
                if (strpos($item, '#') === 0) {
                    //Is an id
                    $Element['attributes'] = ['id' => substr($item, 1)];
                    $Element['text'] = implode("\n", $lines);
                }
            }
        }

        if (isset($Element['attributes'])) {
            foreach ($Element['attributes'] as $name => $value) {
                if ($value === null) {
                    continue;
                }

                $markup .= ' '.$name.'="'.$value.'"';
            }
        }

        if (isset($Element['text'])) {
            $markup .= '>';

            if (isset($Element['handler'])) {
                $markup .= $this->{$Element['handler']}($Element['text'])."\n";
            } else {
                $markup .= $Element['text']."\n";
            }

            $markup .= '</'.$Element['name'].'>';
        } else {
            $markup .= ' />';
        }

        return $markup;
    }


    /**
     * Updated this function so that is does not remove empty lines
     *
     * @param array $lines
     * @return string
     */
    protected function lines(array $lines)
    {
        $CurrentBlock = null;

        foreach ($lines as $line)
        {
            if (chop($line) === '')
            {
                if (isset($CurrentBlock))
                {
                    if (isset($CurrentBlock['element']['text']) && is_string($CurrentBlock['element']['text'])) {
                        //Add empty line if block is not yet finished
                        $CurrentBlock['element']['text'] .= "\n";
                    }
                    $CurrentBlock['interrupted'] = true;
                }

                continue;
            }

            if (strpos($line, "\t") !== false)
            {
                $parts = explode("\t", $line);

                $line = $parts[0];

                unset($parts[0]);

                foreach ($parts as $part)
                {
                    $shortage = 4 - mb_strlen($line, 'utf-8') % 4;

                    $line .= str_repeat(' ', $shortage);
                    $line .= $part;
                }
            }

            $indent = 0;

            while (isset($line[$indent]) and $line[$indent] === ' ')
            {
                $indent ++;
            }

            $text = $indent > 0 ? substr($line, $indent) : $line;

            # ~

            $Line = array('body' => $line, 'indent' => $indent, 'text' => $text);

            # ~

            if (isset($CurrentBlock['continuable']))
            {
                $Block = $this->{'block'.$CurrentBlock['type'].'Continue'}($Line, $CurrentBlock);

                if (isset($Block))
                {
                    $CurrentBlock = $Block;

                    continue;
                }
                else
                {
                    if ($this->isBlockCompletable($CurrentBlock['type']))
                    {
                        $CurrentBlock = $this->{'block'.$CurrentBlock['type'].'Complete'}($CurrentBlock);
                    }
                }
            }

            # ~

            $marker = $text[0];

            # ~

            $blockTypes = $this->unmarkedBlockTypes;

            if (isset($this->BlockTypes[$marker]))
            {
                foreach ($this->BlockTypes[$marker] as $blockType)
                {
                    $blockTypes []= $blockType;
                }
            }

            #
            # ~

            foreach ($blockTypes as $blockType)
            {
                $Block = $this->{'block'.$blockType}($Line, $CurrentBlock);

                if (isset($Block))
                {
                    $Block['type'] = $blockType;

                    if ( ! isset($Block['identified']))
                    {
                        $Blocks []= $CurrentBlock;

                        $Block['identified'] = true;
                    }

                    if ($this->isBlockContinuable($blockType))
                    {
                        $Block['continuable'] = true;
                    }

                    $CurrentBlock = $Block;

                    continue 2;
                }
            }

            # ~

            if (isset($CurrentBlock) and ! isset($CurrentBlock['type']) and ! isset($CurrentBlock['interrupted']))
            {
                $CurrentBlock['element']['text'] .= "\n".$text;
            }
            else
            {
                $Blocks []= $CurrentBlock;

                $CurrentBlock = $this->paragraph($Line);

                $CurrentBlock['identified'] = true;
            }
        }

        # ~

        if (isset($CurrentBlock['continuable']) and $this->isBlockCompletable($CurrentBlock['type']))
        {
            $CurrentBlock = $this->{'block'.$CurrentBlock['type'].'Complete'}($CurrentBlock);
        }

        # ~

        $Blocks []= $CurrentBlock;

        unset($Blocks[0]);

        # ~

        $markup = '';

        foreach ($Blocks as $Block)
        {
            if (isset($Block['hidden']))
            {
                continue;
            }

            $markup .= "\n";
            $markup .= isset($Block['markup']) ? $Block['markup'] : $this->element($Block['element']);
        }

        $markup .= "\n";

        # ~

        return $markup;
    }
}
