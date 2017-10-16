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

namespace Stati\Tests;

use Stati\Site\Site;
use Stati\Tests\TestCase;
use Stati\Entity\Post;
use Stati\Liquid\Template\Template;
use Stati\Tests\TestFileSystem;

class IncludeTagTest extends TestCase
{
    private $fs;

    public function setUp()
    {
        parent::setUp();
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

    public function testIncludeWithLiquidTemplateName()
    {
        $template = new Template();
        $template->setFileSystem(TestFileSystem::fromArray(array(
            'icon-test.svg' => "<svg />",
        )));

        $template->parse("{% include {{ action.icon | prepend: 'icon-' | append: '.svg' }} %}");
        $res = $template->render(['action' => ['icon' => 'test']]);

        $this->assertEquals('<svg />', $res);
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
