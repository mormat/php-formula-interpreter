<?php

namespace Mormat\FormulaInterpreter;

class Executable
{
    /**
     * @param Command\CommandInterface $command
     * @param FunctionInterface[] $functions
     */
    public function __construct(
        protected Command\CommandInterface $command,
        protected $functions = []
    ) {
    }

    public function run($variables = array())
    {
        $context = new Command\CommandContext($variables, $this->functions);
        return $this->command->run($context);
    }
}
