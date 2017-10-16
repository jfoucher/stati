<?php
/**
 * SiteFilterTest.php
 *
 * Created By: jonathan
 * Date: 16/10/2017
 * Time: 14:30
 */

namespace Stati\Tests\Liquid\Filter;

use Liquid\Exception\ParseException;
use Stati\Liquid\Filter\SiteFilter;
use Stati\Liquid\Template\Template;
use Stati\Tests\TestCase;

class SiteFilterTest extends TestCase
{
    public function testNumWordsWithNullShouldReturnZero()
    {
        $template = new Template();

        $template->parse('{{ null | number_of_words }}');
        $this->assertEquals('0', $template->render());
    }

    /**
     * @expectedException \Liquid\Exception\ParseException
     */
    public function testNumWordsWithArray()
    {
        $template = new Template();
        $template->parse('{{ arr | number_of_words }}');
        $template->render(['arr' => ['1', '2', '3']]);
    }

    public function testNumWordsWithVariable()
    {
        $template = new Template();
        $template->parse('{{ a | number_of_words }}');
        $this->assertEquals('2', $template->render(['a' => 'two words']));
    }

    public function testNumWordsWithString()
    {
        $template = new Template();
        $template->parse('{{ "two words" | number_of_words }}');
        $this->assertEquals('2', $template->render());
    }

    public function testJsonifyWithString()
    {
        $template = new Template();
        $template->parse('{{ "string" | tojson }}');
        $this->assertEquals('"string"', $template->render());
    }

    public function testJsonifyWithArray()
    {
        $template = new Template();
        $template->parse('{{ arr | tojson }}');
        $this->assertEquals('[1,2,3]', $template->render(['arr' => [1, 2, 3]]));
    }

    public function testJsonifyWithObject()
    {
        $template = new Template();
        $template->parse('{{ obj | jsonify }}');
        $this->assertEquals('{"a":"b","c":"d"}', $template->render(['obj' => ['a' => 'b', 'c' => 'd']]));
    }

    public function testArrayToStringWithAssociativeArray()
    {
        $template = new Template();
        $template->parse('{{ arr | array_to_sentence_string }}');
        $this->assertEquals('a b c', $template->render(['arr' => ['a' => 'a', 'b' => 'b', 'c' => 'c']]));
    }

    public function testArrayToStringWithArray()
    {
        $template = new Template();
        $template->parse('{{ arr | array_to_sentence_string }}');
        $this->assertEquals('a b c', $template->render(['arr' => ['a', 'b', 'c']]));
    }

    public function testArrayToStringWithString()
    {
        $template = new Template();
        $template->parse('{{ str | array_to_sentence_string }}');
        $this->assertEquals('test a', $template->render(['str' => 'test a']));
    }

    public function testWhereWithArray()
    {
        $template = new Template();
        $template->parse("{% assign filtered = arr | where: 'a', 'b' %}{% for f in filtered %}{{ f | array_to_sentence_string }}{% endfor %}");
        $this->assertEquals('b c', $template->render(['arr' => [['a' => 'a', 'b'=>'c'], ['a' => 'b', 'b' => 'c']]]));
    }

    public function testWhereWithString()
    {
        $template = new Template();
        $template->parse("{{ arr | where: 'a', 'b' }}");
        $this->assertEquals('string', $template->render(['arr' => 'string']));
    }

    public function testWhereWithObject()
    {
        $template = new Template();
        $template->parse("{% assign filtered = arr | where: 'a', 'b' %}{% for f in filtered %} {{ f.a }} {{ f.b }}{% endfor %}");
        $this->assertEquals(' b c', $template->render(['arr' => json_decode(json_encode([['a' => 'a', 'b'=>'c'], ['a' => 'b', 'b' => 'c']]))]));
    }
}
