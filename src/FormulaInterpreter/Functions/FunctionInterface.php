<?php

namespace Mormat\FormulaInterpreter\Functions;

interface FunctionInterface {
    
    /**
     * check if the provided params are valid
     * 
     * @param mixed[] $params
     * @return boolean
     */
    function supports(array $params);
    
    /**
     * execute the function
     * 
     * @param mixed $value
     * @return mixed
     */
    function execute(array $params);
    
    
    /**
     * returns the name of the function
     * 
     * @return string
     */
    function getName();
    
}