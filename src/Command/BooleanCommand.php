<?php

namespace Mormat\FormulaInterpreter\Command;

class BooleanCommand implements CommandInterface {

    public function __construct(
        protected bool $value
    ) { }
    
    public function run(CommandContext $context) {
        return $this->value;
    }
}
