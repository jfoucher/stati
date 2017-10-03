<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
 */

namespace Stati\Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\Regexp;
use Liquid\FileSystem;
use Liquid\Tag\TagInclude as BaseInclude;
use Liquid\Document;
use Liquid\Variable;
use Stati\Liquid\Template\Template;

class TagInclude extends AbstractTag
{

    const VALID_SYNTAX = <<<EOL
    ([\w-]+)\s*=\s*
    (?:"([^"\\]*(?:\\.[^"\\]*)*)"|'([^'\\]*(?:\\.[^'\\]*)*)'|([\w\.-]+))
EOL;
    const VARIABLE_SYNTAX = <<<EOL
    !
    (?<variable>[^{]*(\{\{\s*[\w\-\.]+\s*(\|.*)?\}\}[^\s{}]*)+)
    (?<params>.*)
    !x
EOL;

    /**
     * @var string The name of the template
     */
    private $templateName;

    /**
     * @var bool True if the variable is a collection
     */
    private $collection;

    /**
     * @var mixed The value to pass to the child template as the template name
     */
    private $variable;

    /**
     * @var Document The Document that represents the included template
     */
    private $document;

    /**
     * @var string The Source Hash
     */
    protected $hash;

    /**
     * @var string the params passed to the wub template
     */
    protected $params;

    /**
     * @var array The attributes that we have to extract from the context
     */
    protected $contextAttributes = [];

    /**
     * Constructor
     *
     * @param string $markup
     * @param array $tokens
     * @param FileSystem $fileSystem
     *
     * @throws \Liquid\LiquidException
     */
    public function __construct($markup, array &$tokens, FileSystem $fileSystem = null)
    {
        $variableSyntax = new Regexp(self::VARIABLE_SYNTAX);
        $splitSyntax = new Regexp('/\s+/');
        if ($variableSyntax->match(trim($markup))){
            $this->templateName = str_replace(['"', "'"], '', $variableSyntax->matches['variable']);
            $this->params = $variableSyntax->matches['params'];
        } else {
            $ex = $splitSyntax->split(trim($markup), 2);
            if (count($ex)) {
                $this->templateName = str_replace(['"', "'"], '', $ex[0]);
            }
            if (count($ex) > 1) {
                $this->params = $ex[1];
            }
        }
        if (!$this->templateName) {
            throw new LiquidException('Incorrect syntax for include tag');
        }
        if ($this->params) {
            $this->validateParams();
        }
        $this->extractVariables();
        parent::__construct($markup, $tokens, $fileSystem);

    }

    protected function extractAttributes($markup)
    {
        // Do nothing
    }

    protected function extractVariables()
    {
        $this->attributes = array();
        $re = '%
        ([\w-]+)\s*=\s*
        (?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|([\w\.-]+))
      %x';

        preg_match_all($re, $this->params, $matches, PREG_SET_ORDER, 0);

        $includeVars = [];
        foreach ($matches as $match) {
            if (isset($match[1])) {
                if (isset($match[2]) && strlen($match[2])) {
                    $this->attributes[$match[1]] = $match[2];
                }
                if (isset($match[3]) && strlen($match[3])) {
                    // is a string
                    $this->attributes[$match[1]] = $match[3];
                }
                if (isset($match[4]) && strlen($match[4])) {
                    // is a variable
                    $this->contextAttributes[$match[1]] = $match[4];
                }
            }
        }
    }


    /**
     * Parses the tokens
     *
     * @param array $tokens
     *
     * @throws \Liquid\LiquidException
     */
    public function parse(array &$tokens)
    {
        if ($this->fileSystem === null) {
            throw new LiquidException("No file system");
        }


        // read the source of the template and create a new sub document
        $source = $this->fileSystem->readTemplateFile($this->templateName);
        $this->hash = md5($source);

        $cache = Template::getCache();

        if (isset($cache)) {
            if (($this->document = $cache->read($this->hash)) != false && $this->document->checkIncludes() != true) {
            } else {
                $templateTokens = Template::tokenize($source);
                $this->document = new Document($templateTokens, $this->fileSystem);
                $cache->write($this->hash, $this->document);
            }
        } else {
            $templateTokens = Template::tokenize($source);
            $this->document = new Document($templateTokens, $this->fileSystem);
        }
    }

    /**
     * check for cached includes
     *
     * @return boolean
     */
    public function checkIncludes()
    {
        $cache = Template::getCache();

        if ($this->document->checkIncludes() == true) {
            return true;
        }

        $source = $this->fileSystem->readTemplateFile($this->templateName);

        if ($cache->exists(md5($source)) && $this->hash === md5($source)) {
            return false;
        }

        return true;
    }

    /**
     * Renders the node
     *
     * @param Context $context
     *
     * @return string
     */
    public function render(Context $context)
    {
        $result = '';
//        $variable = $context->get($this->variable);
//
        $context->push();

        $includeVars = [];
        foreach ($this->attributes as $key => $value) {
            $includeVars[$key] = $value;
        }

        foreach ($this->contextAttributes as $key => $value) {
            $includeVars[$key] = $context->get($value);
        }
        $context->set('include', $includeVars);
        $result .= $this->document->render($context);

        $context->pop();

        return $result;
    }


    private function validateParams()
    {
        $fullValidSyntax = '%\A\s*(?:
        ([\w-]+)\s*=\s*
        (?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'|([\w\.-]+))
      (?=\s|\z)\s*)*\z%x';
        $r = preg_match($fullValidSyntax, $this->params, $matches, PREG_OFFSET_CAPTURE);

        if (!$r) {
            throw new LiquidException(<<<EOL
Invalid syntax for include tag: $this->params
Valid syntax: {% include file.ext param='value' param2='value' %}
EOL
            );
        }
    }
}
