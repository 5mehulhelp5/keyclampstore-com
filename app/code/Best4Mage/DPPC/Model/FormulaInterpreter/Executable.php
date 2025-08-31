<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter;

use Best4Mage\DPPC\Model\FormulaInterpreter\Parser;
use Best4Mage\DPPC\Model\FormulaInterpreter\Command;

/**
 * Description of Compiler
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class Executable
{
    
    /**
     * @var Command\CommandInterface
     */
    protected $command;
    
    /**
     * @var \ArrayObject
     */
    protected $variables;
    
    function __construct(Command\CommandInterface $command, \ArrayObject $variables)
    {
        $this->command = $command;
        $this->variables = $variables;
    }

    function run($variables = [])
    {
        $this->variables->exchangeArray($variables);
        return $this->command->run();
    }
}
