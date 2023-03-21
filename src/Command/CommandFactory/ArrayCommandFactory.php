<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\ArrayCommand;

/**
 * Create an array command
 *
 * @author mathieu
 */
class ArrayCommandFactory implements CommandFactoryInterface {
    
    /**
     * @var CommandFactoryInterface
     */
    protected $itemCommandFactory;
    
    public function __construct(CommandFactoryInterface $itemCommandFactory) {
        $this->itemCommandFactory = $itemCommandFactory;
    }
    
    public function create($options) {
        return new ArrayCommand(
            array_map(function($item) {
                return $this->itemCommandFactory->create($item);
            }, $options['value'])
        );
    }

}
