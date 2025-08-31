<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Parser;

use Best4Mage\DPPC\Model\FormulaInterpreter\Parser\ParserException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class NumericParser implements ParserInterface
{
    
    function parse($expression)
    {
        
        $expression = trim($expression);
        
        if (!preg_match('/^[0-9]*(\.[0-9]*){0,1}$/', $expression)) {
            throw new ParserException($expression);
        }
        
        return $infos = [
            'type' => 'numeric',
            'value' => floatval($expression),
        ];
    }
}
