<?php

namespace Mormat\FormulaInterpreter\Command;

interface CommandInterface
{
    public function run(CommandContext $context);
}
