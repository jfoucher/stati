<?php
/**
 * StaticFile.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 15:04
 */

namespace Stati\Entity;

use Symfony\Component\Finder\SplFileInfo;

class StaticFile extends SplFileInfo
{
    public function get($item) {
        if ($item === 'path') {
            return $this->getRelativePathname();
        }
        if ($item === 'modified_time') {
            return $this->getMTime();
        }
        if ($item === 'name') {
            var_dump('GETTING NAME');
            var_dump($this->getBasename());
            return $this->getBasename();
        }
        if ($item === 'basename') {
            return pathinfo($this->getRelativePathname(), PATHINFO_BASENAME);
        }
        if ($item === 'extname') {
            return '.'.pathinfo($this->getRelativePathname(), PATHINFO_EXTENSION);
        }
        return null;
    }

    public function __get($item) {
        return $this->get($item);
    }
}