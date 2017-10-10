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
                $markup .= $this->{$Element['handler']}($Element['text']);
            } else {
                $markup .= $Element['text'];
            }

            $markup .= '</'.$Element['name'].'>';
        } else {
            $markup .= ' />';
        }

        return $markup;
    }
}
