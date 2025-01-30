<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\ArrayCommand;

use Mormat\FormulaInterpreter\Command\CommandInterface;

class ArrayCommandFactory implements CommandFactoryInterface
{
    public function __construct(
        protected CommandFactoryInterface $itemCommandFactory
    ) {
        $this->itemCommandFactory = $itemCommandFactory;
    }
    
    public function create($options): CommandInterface
    {
        return new ArrayCommand(
            array_map(
                fn($item) => $this->itemCommandFactory->create($item),
                $options['value']
            )
        );
    }
}
