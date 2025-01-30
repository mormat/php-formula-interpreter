<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use Mormat\FormulaInterpreter\Command\CommandInterface;

interface CommandFactoryInterface
{
    /**
     * @param array $options
     */
    public function create($options): CommandInterface;
}
