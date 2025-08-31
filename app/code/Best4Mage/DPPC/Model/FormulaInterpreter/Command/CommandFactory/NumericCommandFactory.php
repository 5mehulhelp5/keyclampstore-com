<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory;

use Best4Mage\DPPC\Model\FormulaInterpreter\Command\NumericCommand;
use Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class NumericCommandFactory implements CommandFactoryInterface
{
    
    public function create($options)
    {
        if (!isset($options['value'])) {
            throw new CommandFactoryException();
        }
        
        return new NumericCommand($options['value']);
    }
}
