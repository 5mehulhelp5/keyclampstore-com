<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Parser;

use Best4Mage\DPPC\Model\FormulaInterpreter\Parser\ParserException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class CompositeParser implements ParserInterface
{
    
    protected $parsers = [];
    
    public function addParser(ParserInterface $parser)
    {
        $this->parsers[] = $parser;
    }
    
    function parse($expression)
    {
        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($expression);
            } catch (ParserException $e) {
                if ($e->getExpression() != $expression) {
                    throw $e;
                }
            }
        }
        
        throw new ParserException($expression);
    }
}
