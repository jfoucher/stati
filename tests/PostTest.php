<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests;

use Stati\Site\Site;
use Stati\Tests\TestCase;
use Stati\Entity\Post;
use Symfony\Component\Finder\SplFileInfo;

class PostTest extends TestCase
{
    public function setUp()
    {
        chdir(dirname(__FILE__).'/fixtures/postTest/');
    }
    public function tearDown()
    {
        chdir(dirname(__FILE__));
    }

    public function testSimplePost()
    {
        $site = new Site([
            'permalink' => '/:year/:month/:day/:title.html',
            'url' => 'https://test.com',
            'baseurl' => '',
        ]);
        $file = new SplFileInfo('2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $post = new Post($file, $site);

        $this->assertEquals('Simple post', $post->title);
        $this->assertEquals('Post excerpt.', $post->excerpt);
        $this->assertEquals('/2017/08/10/simple-post.html', $post->url);
        $this->assertEquals('simple-post', $post->slug);
        $this->assertEquals('2017-08-10T00:00:00+00:00', $post->date);

        $expectedContent = '<p>Single paragraph</p>
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
</pre></div>';
        $this->assertEquals($expectedContent, $post->content);
    }
}
