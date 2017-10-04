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
    public function testPath()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/sass/style.scss', 'sass', 'style.scss');
        $doc = new Sass($file, $this->site);
        $this->assertEquals('/sass/style.css', $doc->getPath());
    }

    public function testContent()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/sass/style.scss', 'sass', 'style.scss');
        $doc = new Sass($file, $this->site);
        echo $doc->getContent();
        $this->assertEquals('.head a {
  color: #333; }
', $doc->getContent());
    }
}
