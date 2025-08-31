<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory;

use Best4Mage\DPPC\Model\FormulaInterpreter\Command\FunctionCommand;
use Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class FunctionCommandFactory implements CommandFactoryInterface
{
    
    protected $functions = [];
    
    /**
     * @var CommandFactoryInstance
     */
    protected $argumentCommandFactory;
    
    function __construct(CommandFactoryInterface $argumentCommandFactory)
    {
        $this->argumentCommandFactory = $argumentCommandFactory;
    }
    
    public function registerFunction($name, $callable)
    {
        $this->functions[$name] = $callable;
    }
    
    public function create($options)
    {
        
        if (!isset($options['name'])) {
            throw new CommandFactoryException('Missing option "name"');
        }
        
        if (!isset($this->functions[$options['name']])) {
            throw new \FormulaInterpreter\Exception\UnknownFunctionException($options['name']);
        }
        
        $argumentCommands = [];
        if (isset($options['arguments'])) {
            foreach ($options['arguments'] as $argumentOptions) {
                $argumentCommands[] = $this->argumentCommandFactory->create($argumentOptions);
            }
        }
        
        return new FunctionCommand($this->functions[$options['name']], $argumentCommands);
    }
}
