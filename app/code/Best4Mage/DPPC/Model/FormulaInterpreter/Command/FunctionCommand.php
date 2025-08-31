<?php

namespace Best4Mage\DPPC\Model\FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 * @modified by Best4Mage
 */
class FunctionCommand implements CommandInterface
{
    
    protected $callable;
    
    protected $argumentCommands = [];
    
    function __construct($callable, $argumentCommands = [])
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException();
        }
        
        $this->callable = $callable;

        foreach ($argumentCommands as $argumentCommand) {
            if (!($argumentCommand instanceof CommandInterface)) {
                throw new \InvalidArgumentException();
            }
        }
        
        $reflection = new \ReflectionFunction($this->callable);
        if (sizeof($argumentCommands) < $reflection->getNumberOfRequiredParameters()) {
            throw new \FormulaInterpreter\Exception\NotEnoughArgumentsException();
        }
        
        $this->argumentCommands = $argumentCommands;
    }

    public function run()
    {
        $arguments = [];
        foreach ($this->argumentCommands as $command) {
            $arguments[] = $command->run();
        }
        
        return call_user_func_array($this->callable, $arguments);
    }
}
