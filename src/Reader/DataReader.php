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

namespace Stati\Reader;

use Stati\Entity\Page;
use Stati\Entity\Sass;
use Symfony\Component\Finder\Finder;
use Stati\Reader\Reader;
use Stati\Parser\Yaml;

/**
 * Copies files and folders that do not start with _ and do not have frontmatter
 * Class FilesRenderer
 * @package Stati\Renderer
 */
class DataReader extends Reader
{
    public function read()
    {
        $config = $this->site->getConfig();
        $dataDir = $config['data_dir'];
        if (!is_dir($dataDir)) {
            return [];
        }
        // Get top level files and parse
        $finder = new Finder();
        $finder
            ->in($dataDir)
            ->files()
            ->notName('/^_/')
            ->name('/(.yml|.yaml|.json)$/')
        ;

        foreach ($config['exclude'] as $exclude) {
            if (strpos($exclude, '/') === 0) {
                $exclude = substr($exclude, 1);
            }
            if (strpos($exclude, '*') !== false) {
                //If pattern is a glob, treat as such
                $finder->notName($exclude);
                $finder->notPath($exclude);
            } else {
                // Otherwise, match start and end of string
                $finder->notName('|^'.$exclude.'$|');
                $finder->notPath('|^'.$exclude.'$|');
            }
        }

        $data = [];
        foreach ($finder as $file) {
            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            if ($file->getExtension() === 'json') {
                $data[$name] = json_decode($file->getContents(), true);
            } else {
                $data[$name] = Yaml::parse($file->getContents());
            }
        }

        return $data;
    }
}
