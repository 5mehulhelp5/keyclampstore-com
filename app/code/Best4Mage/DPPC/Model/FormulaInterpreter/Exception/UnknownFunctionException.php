<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Exception;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class UnknownFunctionException extends \Exception
{
    
    protected $name;
    
    function __construct($name)
    {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown function "%s"', $name));
    }
    
    public function getName()
    {
        return $this->name;
    }
}
