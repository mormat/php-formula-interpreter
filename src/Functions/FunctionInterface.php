<?php

namespace Mormat\FormulaInterpreter\Functions;

interface FunctionInterface
{
    /**
     * check if the provided params are valid
     * @param mixed[] $params
     */
    public function supports(array $params): bool;
    
    /**
     * execute the function
     * @param mixed[] $params
     */
    public function execute(array $params): mixed;
    
    /**
     * returns the name of the function
     */
    public function getName(): string;
}
