<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Command\CommandFactory;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
interface CommandFactoryInterface {
    
    /**
     * @param array $options
     * @return FormulaInterpreter\Command\CommandInterface
     */
    function create($options);
    
}

