<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class FunctionParser implements ParserInterface {
    
    /**
     * @var ParserInterface
     */
    protected $argumentParser;
    
    function __construct(ParserInterface $argumentParser) {
        $this->argumentParser = $argumentParser;
    }

    function parse($expression) {
        $expression = trim($expression);
        
        if (!preg_match('/^(\w)+\(/', $expression)) {
            throw new ParserException($expression);
        }
        if (substr($expression, -1, 1) != ')') {
            throw new ParserException($expression);
        }
        
        $open = strpos($expression, '(');

        $results = array(
            'type' => 'function',
            'name' => substr($expression, 0, $open),
        );

        $arguments = trim(substr($expression, $open + 1, -1));
        if ($arguments != '') {
            $parsedArguments = array();
            foreach ($this->explodeArguments($arguments) as $argument) {
                $parsedArguments[] = $this->argumentParser->parse($argument);
            }
            $results['arguments'] = $parsedArguments;
        }

        return $results;
    }

    protected function explodeArguments($expression) {

        $arguments = array();
        $previous = 0;
        $parenthesis = 0;
        for ($position = 0; $position < strlen($expression); $position++) {
            switch ($expression[$position]) {
                case ',':
                    if ($parenthesis == 0) {
                        $arguments[] = substr($expression, $previous, $position);
                        $position++;
                        $previous = $position;
                    }
                    break;
                case '(':
                    $parenthesis++;
                    break;
                case ')':
                    $parenthesis--;
                    break;
            }
        }
        $arguments[] = substr($expression, $previous, $position);

        return $arguments;
    }

}

?>
