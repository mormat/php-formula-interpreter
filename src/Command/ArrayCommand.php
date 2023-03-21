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
    
    public function run() {
        return array_map(
            function(CommandInterface $item) {
                return $item->run();
            },
            $this->itemCommands
        );
    }

}
