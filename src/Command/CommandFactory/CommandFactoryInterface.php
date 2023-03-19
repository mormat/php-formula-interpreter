<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
interface CommandFactoryInterface {
    
    /**
     * @param array $options
     * @return FormulaInterpreter\Command\CommandInterface
     */
    function create($options);
    
}

