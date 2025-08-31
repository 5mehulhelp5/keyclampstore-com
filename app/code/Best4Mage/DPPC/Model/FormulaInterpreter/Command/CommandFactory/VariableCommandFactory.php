<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory;

use Best4Mage\DPPC\Model\FormulaInterpreter\Command\VariableCommand;
use Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class VariableCommandFactory implements CommandFactoryInterface
{
    
    protected $variables;
    
    function __construct($variables)
    {
        $this->variables = $variables;
    }
    
    public function create($options)
    {
        if (!isset($options['name'])) {
            throw new CommandFactoryException();
        }
        
        return new VariableCommand(
            $options['name'],
            $this->variables
        );
    }
}
