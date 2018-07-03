<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter;

/**
 * Description of Compiler
 *
 * @author mathieu
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

    public function __construct(Command\CommandInterface $command, \ArrayObject $variables)
    {
        $this->command = $command;
        $this->variables = $variables;
    }

    public function run($variables = [])
    {
        $this->variables->exchangeArray($variables);
        return $this->command->run();
    }

    public function getParameters()
    {
        return $this->command->getParameters();
    }
}
