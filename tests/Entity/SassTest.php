<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Tests\Entity;

use Stati\Entity\Sass;
use Stati\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class SassTest extends TestCase
{
    protected $file;
    public function setUp()
    {
        parent::setUp();
        $this->file = $this->createFile(
            '/sass/style.sass',
            <<<EOL
---
---

.head {
    a {
        color: #333;;
    }
}
EOL
);
    }

    public function testPath()
    {
        $doc = new Sass($this->file, $this->site);
        $this->assertEquals('/sass/style.css', $doc->getOutputPath());
    }

    public function testContent()
    {
        $doc = new Sass($this->file, $this->site);
        $this->assertEquals('.head a {
  color: #333; }
', $doc->getContent());
    }
}
