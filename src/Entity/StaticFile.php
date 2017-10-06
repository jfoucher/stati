<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Entity;

use Symfony\Component\Finder\SplFileInfo;

class StaticFile extends SplFileInfo
{
    public function get($item)
    {
        if ($item === 'path') {
            return $this->getRelativePathname();
        }
        if ($item === 'modified_time') {
            return date_create_from_format('U', (string)$this->getMTime())->format(DATE_RFC3339);
        }
        if ($item === 'name') {
            return $this->getFilename();
        }
        if ($item === 'basename') {
            return pathinfo($this->getRelativePathname(), PATHINFO_FILENAME);
        }
        if ($item === 'extname') {
            return '.'.pathinfo($this->getRelativePathname(), PATHINFO_EXTENSION);
        }
        return null;
    }

    public function field_exists($item)
    {
        return in_array($item, ['path', 'modified_time', 'name', 'basename', 'extname']);
    }

    public function __get($item)
    {
        return $this->get($item);
    }
}
