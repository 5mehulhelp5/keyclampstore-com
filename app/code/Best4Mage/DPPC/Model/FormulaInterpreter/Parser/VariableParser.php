<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Parser;

use Best4Mage\DPPC\Model\FormulaInterpreter\Parser\ParserException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class VariableParser implements ParserInterface
{
    
    function parse($expression)
    {
        $expression = trim($expression);
        
        if (!preg_match('/^([a-zA-Z_]+[0-9]*)+$/', $expression)) {
            throw new ParserException($expression);
        }
        
        return [
            'type' => 'variable',
            'name' => $expression,
        ];
    }
}
