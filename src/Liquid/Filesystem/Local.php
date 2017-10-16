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

namespace Stati\Liquid\Filesystem;

use Liquid\Exception\NotFoundException;
use Liquid\Exception\ParseException;
use Liquid\Liquid;
use Liquid\Regexp;
use Liquid\FileSystem;

class Local implements FileSystem
{
    protected $root;

    /**
     * Constructor
     *
     * @param string $root The root path for templates
     * @throws \Liquid\Exception\NotFoundException
     */
    public function __construct($root)
    {
        // since root path can only be set from constructor, we check it once right here
        if (!empty($root)) {
            $realRoot = realpath($root);
            if ($realRoot === false) {
                throw new NotFoundException("Root path could not be found: '$root'");
            }
            $root = $realRoot;
        }

        $this->root = $root;
    }


    /**
     * Retrieve a template file
     *
     * @param string $templatePath
     *
     * @return string template content
     */
    public function readTemplateFile($templatePath)
    {
        return file_get_contents($this->fullPath($templatePath));
    }

    /**
     * Resolves a given path to a full template file path, making sure it's valid
     *
     * @param string $templatePath
     *
     * @throws \Liquid\Exception\ParseException
     * @throws \Liquid\Exception\NotFoundException
     * @return string
     */
    public function fullPath($templatePath)
    {
        if (empty($templatePath)) {
            throw new ParseException("Empty template name");
        }

        $nameRegex = Liquid::get('INCLUDE_ALLOW_EXT')
            ? new Regexp('/^[^.\/][-a-zA-Z0-9_\.\/]+$/')
            : new Regexp('/^[^.\/][-a-zA-Z0-9_\/]+$/');

        if (!$nameRegex->match($templatePath)) {
            throw new ParseException("Illegal template name '$templatePath'");
        }

        $templateDir = dirname($templatePath);
        $templateFile = basename($templatePath);

        if (!Liquid::get('INCLUDE_ALLOW_EXT')) {
            $templateFile = Liquid::get('INCLUDE_PREFIX') . $templateFile . '.' . Liquid::get('INCLUDE_SUFFIX');
        }

        $fullPath = join(DIRECTORY_SEPARATOR, array($this->root, $templateDir, $templateFile));

        $realFullPath = realpath($fullPath);
        if ($realFullPath === false) {
            throw new NotFoundException("File not found: $fullPath");
        }

        if (strpos($realFullPath, $this->root) !== 0) {
            throw new NotFoundException("Illegal template full path: {$realFullPath} not under {$this->root}");
        }

        return $realFullPath;
    }
}
