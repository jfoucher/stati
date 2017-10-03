<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Tests;

use Stati\Site\Site;
use Stati\Tests\TestCase;
use Stati\Entity\Post;
use Stati\Liquid\Template\Template;
use Stati\Tests\TestFileSystem;

class IncludeTagTest extends TestCase
{
    private $fs;

    protected function setUp()
    {
        $this->fs = TestFileSystem::fromArray(array(
            'inner' => "Inner: {{ inner }}{{ other }}",
            'example' => "Example: {% include 'inner' %}",
        ));
    }

    protected function tearDown()
    {
        // PHP goes nuts unless we unset it
        unset($this->fs);
    }

    public function testIncludeWithSingleVariable()
    {
        $template = new Template();
        $template->setFileSystem(TestFileSystem::fromArray(array(
            'example' => "{{ include.number }}",
        )));
        $template->parse("{% include 'example' number=3 %}");
        $this->assertEquals('3', $template->render());
    }

    public function testIncludeWithTwoVariables()
    {
        $template = new Template();
        $template->setFileSystem(TestFileSystem::fromArray(array(
            'example' => "{{ include.number }} {{include.second}}",
        )));
        $template->parse("{% include 'example' number=3 second = 'aaa.cs' %}");
        $this->assertEquals('3 aaa.cs', $template->render(['site' => ['docs' => ['one', 'two']]]));
    }

    public function testIncludeWithPassedVariable()
    {
        $template = new Template();
        $template->setFileSystem(TestFileSystem::fromArray(array(
            'example' => "{% for doc in include.docs %}{{ doc }}{% endfor %}{{include.test}}{{a}}",
        )));
        $template->parse("{% include 'example' test='c'  docs=site.docs %}");
        $this->assertEquals('abcd', $template->render(['a'=>'d', 'site' => ['docs' => ['a', 'b']]]));
    }
}
