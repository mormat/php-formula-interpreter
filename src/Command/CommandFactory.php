<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class CommandFactory implements CommandFactory\CommandFactoryInterface {
    
    protected $factories = array();
    
    public function registerFactory($type, CommandFactory\CommandFactoryInterface $factory) {
        $this->factories[$type] = $factory;
    }
    
    public function create($options) {
        if (!isset($options['type'])) {
            throw new CommandFactory\CommandFactoryException('Missing argument "type"');
        }
        
        if (!isset($this->factories[$options['type']])) {
            throw new CommandFactory\CommandFactoryException(sprintf('Unknown factory type "%s"', $options['type']));
        }
        
        return $this->factories[$options['type']]->create($options);
    }
}
