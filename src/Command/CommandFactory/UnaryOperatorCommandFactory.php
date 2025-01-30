<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\UnaryOperatorCommand;

class UnaryOperatorCommandFactory implements CommandFactoryInterface
{
    public function __construct(
        protected CommandFactoryInterface $childCommandFactory
    ) {
    }

    public function create($options): CommandInterface
    {
        $value = $this->childCommandFactory->create($options['value']);
        return new UnaryOperatorCommand($options['operator'], $value);
    }
}
