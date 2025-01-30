<?php

namespace Mormat\FormulaInterpreter\Command;

class ArrayCommand implements CommandInterface
{
    /**
     * @param CommandInterface[] $itemCommands
     */
    public function __construct(
        protected $itemCommands
    ) {
        $this->itemCommands = $itemCommands;
    }
    
    public function run(CommandContext $context)
    {
        return array_map(
            function (CommandInterface $item) use ($context) {
                return $item->run($context);
            },
            $this->itemCommands
        );
    }
}
