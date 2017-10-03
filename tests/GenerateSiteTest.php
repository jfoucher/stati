<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests;

use Stati\Tests\TestCase;
use Stati\Entity\Post;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application;
use Stati\Command\GenerateCommand;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class GenerateSiteTest extends TestCase
{
    public function setUp()
    {
        chdir(dirname(__FILE__).'/fixtures/layoutTest/');
    }

    public function tearDown()
    {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./_site', FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir('./_site');
        chdir(dirname(__FILE__));
    }

    public function testPostSaved()
    {
        $this->markTestSkipped();
        $arguments = [];
        $input = new ArrayInput($arguments);
        $output = new ConsoleOutput();
        $application = new Application('Stati', '@package_version@');
        $application->add(new GenerateCommand());
        $command = $application->find('generate');
        $returnCode = $command->run($input, $output);
        $this->assertEquals(0, $returnCode);
        $this->assertEquals(true, is_dir('./_site'));
        $this->assertEquals(true, is_dir('./_site/2017/08'));
        $this->assertEquals(true, is_file('./_site/2017/08/simple-post.html'));
        $this->assertEquals('<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->


<body>

   Test Site
Simple post

   <div class="content">
      <div class="container">
         <div class="post">
  
  <h1 class="postTitle"><a href="/2017/08/simple-post.html">Simple post</a></h1>
  <p class="meta">August 10, 2017 | <span class="time">0</span> Minute Read</p>
  
  <!-- <div class="featuredImage">
      <img src="/uploads/2017/08/leanr.jpg" alt="" />
    </div> -->

  <p>Single paragraph</p>
<p class="image">
<img src="/images/image.jpg" alt="Image alt tag" /></p>
<blockquote>
<p><em>&quot;Italic blockquote&quot;</em></p>
</blockquote>
<pre><code class="language-js">    window.addEventListener(\'message\', this.handleFrameTasks.bind(this))</code></pre>
<div class="highlight"><pre>    <span class="kd">var</span> <span class="nx">data</span> <span class="o">=</span> <span class="p">{</span>
        <span class="nx">action</span><span class="o">:</span> <span class="s1">\'deleteBlock\'</span><span class="p">,</span>
        <span class="nx">blockId</span><span class="o">:</span> <span class="nx">blockId</span><span class="p">,</span>
    <span class="p">};</span>
    <span class="nx">top</span><span class="p">.</span><span class="nx">postMessage</span><span class="p">(</span><span class="nx">data</span><span class="p">,</span> <span class="s1">\'*\'</span><span class="p">);</span>
</pre></div>

</div>
      </div>
   </div><!-- end .content -->

    
</body>

</html>', file_get_contents('./_site/2017/08/simple-post.html'));
    }
}
