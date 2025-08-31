<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command\CommandFactory;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
interface CommandFactoryInterface
{
    
    /**
     * @param array $options
     * @return FormulaInterpreter\Command\CommandInterface
     */
    function create($options);
}
