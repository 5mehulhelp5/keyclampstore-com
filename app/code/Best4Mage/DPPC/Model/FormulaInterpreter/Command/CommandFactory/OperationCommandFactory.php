<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory;

use Best4Mage\DPPC\Model\FormulaInterpreter\Command\OperationCommand;
use Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class OperationCommandFactory implements CommandFactoryInterface
{
    
    /**
     * @var CommandFactoryInterface
     */
    protected $operandCommandFactory;
    
    function __construct(CommandFactoryInterface $operandCommandFactory)
    {
        $this->operandCommandFactory = $operandCommandFactory;
    }
    
    public function create($options)
    {

        if (!isset($options['firstOperand'])) {
            throw new CommandFactoryException();
        }
        
        $firstOperand = $this->operandCommandFactory->create($options['firstOperand']);
        $command = new OperationCommand($firstOperand);
        
        if (isset($options['otherOperands'])) {
            foreach ($options['otherOperands'] as $option) {
                if (isset($option['operator']) && isset($option['value'])) {
                    $command->addOperand(
                        $option['operator'],
                        $this->operandCommandFactory->create($option['value'])
                    );
                }
            }
        }
        
        return $command;
    }
}
