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

namespace Stati\Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\Regexp;
use Liquid\FileSystem;
use Liquid\Tag\TagInclude as BaseInclude;
use Liquid\Document;
use Liquid\Variable;
use Stati\Liquid\Template\Template;

class TagInclude extends AbstractTag
{
    const VALID_SYNTAX = <<<EOL
    ([\w-]+)\s*=\s*
    (?:"([^"\\]*(?:\\.[^"\\]*)*)"|'([^'\\]*(?:\\.[^'\\]*)*)'|([\w\.-]+))
EOL;
    const VARIABLE_SYNTAX = '#(?<variable>[^{]*(\{\{\s*[\w\-\.]+\s*(\|.*)?\}\}[^\s{}]*)+)(?<params>.*)#x';

    /**
     * @var string The name of the template
     */
    private $templateName;

    /**
     * @var Document The Document that represents the included template
     */
    private $document;

    /**
     * @var string The Source Hash
     */
    protected $hash;

    /**
     * @var string the params passed to the wub template
     */
    protected $params;

    /**
     * @var array The attributes that we have to extract from the context
     */
    protected $contextAttributes = [];

    /**
     * Constructor
     *
     * @param string $markup
     * @param array $tokens
     * @param FileSystem $fileSystem
     *
     * @throws \Liquid\LiquidException
     */
    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null)
    {
        $variableSyntax = new Regexp(self::VARIABLE_SYNTAX);
        $splitSyntax = new Regexp('/\s+/');

        $re = '!(?<variable>[^{]*(\{\{\s*[\w\-\.]+\s*(\|.*)?\}\}[^\s{}]*)+)(?<params>.*)!x';
        $str = trim($markup);

        if (preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0)) {
            $toks = Template::tokenize($matches['variable'][0]);
            $tmpl = new Document($toks);
            $this->templateName = $tmpl;
            $this->params = $matches['params'][0];
        } else {
            $parts = $splitSyntax->split(trim($markup), 2);
            if (count($parts)) {
                $this->templateName = str_replace(['"', "'"], '', $parts[0]);
            }
            if (count($parts) > 1) {
                $this->params = $parts[1];
            }
        }
        if (!$this->templateName) {
            throw new LiquidException('Incorrect syntax for include tag');
        }
        if ($this->params) {
            $this->validateParams();
        }
        $this->extractVariables();
        parent::__construct($markup, $tokens, $fileSystem);
    }

    protected function extractAttributes($markup)
    {
        // Do nothing
    }

    protected function extractVariables()
    {
        $this->attributes = array();
        $regex = '%
        ([\w-]+)\s*=\s*
        (?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|([\w\.-]+))
      %x';

        preg_match_all($regex, $this->params, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            if (isset($match[1])) {
                if (isset($match[2]) && strlen($match[2])) {
                    $this->attributes[$match[1]] = $match[2];
                }
                if (isset($match[3]) && strlen($match[3])) {
                    // is a string
                    $this->attributes[$match[1]] = $match[3];
                }
                if (isset($match[4]) && strlen($match[4])) {
                    // is a variable
                    $this->contextAttributes[$match[1]] = $match[4];
                }
            }
        }
    }


    /**
     * Parses the tokens
     *
     * @param array $tokens
     *
     * @throws \Liquid\LiquidException
     */
    public function parse(array &$tokens)
    {
        if ($this->fileSystem === null) {
            throw new LiquidException("No file system");
        }
    }

    /**
     * Renders the node
     *
     * @param Context $context
     *
     * @return string
     */
    public function render(Context $context)
    {
        $result = '';

        if (!is_string($this->templateName) && get_class($this->templateName) === 'Liquid\Document') {
            $this->templateName = $this->templateName->render($context);
        }

        // read the source of the template and create a new sub document

        $source = $this->fileSystem->readTemplateFile($this->templateName);
        $this->hash = md5($source);

        $cache = Template::getCache();

        if (isset($cache)) {
            if (($this->document = $cache->read($this->hash)) != false && $this->document->checkIncludes() != true) {
            } else {
                $templateTokens = Template::tokenize($source);
                $this->document = new Document($templateTokens, $this->fileSystem);
                $cache->write($this->hash, $this->document);
            }
        } else {
            $templateTokens = Template::tokenize($source);
            $this->document = new Document($templateTokens, $this->fileSystem);
        }

//        $variable = $context->get($this->variable);
//
        $context->push();

        $includeVars = [];
        foreach ($this->attributes as $key => $value) {
            $includeVars[$key] = $value;
        }

        foreach ($this->contextAttributes as $key => $value) {
            $includeVars[$key] = $context->get($value);
        }
        $context->set('include', $includeVars);
        $result .= $this->document->render($context);

        $context->pop();

        return $result;
    }


    private function validateParams()
    {
        $fullValidSyntax = '%\A\s*(?:
        ([\w-]+)\s*=\s*
        (?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|([\w\.-]+))
      (?=\s|\z)\s*)*\z%x';
        $valid = preg_match($fullValidSyntax, $this->params);

        if (!$valid) {
            throw new LiquidException(
                <<<EOL
Invalid syntax for include tag: $this->params
Valid syntax: {% include file.ext param='value' param2='value' %}
EOL
            );
        }
    }
}
