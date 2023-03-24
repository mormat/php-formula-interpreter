<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Execute an array
 *
 * @author mathieu
 */
class ArrayCommand implements CommandInterface {
    
    /**
     * @var CommandInterface[]
     */
    protected $itemCommands;
    
    public function __construct($itemCommands) {
        $this->itemCommands = $itemCommands;
    }
    
    public function run(CommandContext $context) {
        return array_map(
            function(CommandInterface $item) use($context) {
                return $item->run($context);
            },
            $this->itemCommands
        );
    }

}
