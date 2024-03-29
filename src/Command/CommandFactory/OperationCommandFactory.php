<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\OperationCommand;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class OperationCommandFactory implements CommandFactoryInterface {
    
    /**
     * @var CommandFactoryInterface
     */
    protected $operandCommandFactory;
    
    function __construct(CommandFactoryInterface $operandCommandFactory) {
        $this->operandCommandFactory = $operandCommandFactory;
    }
    
    public function create($options) {

        if (!isset($options['firstOperand'])) {
            throw new CommandFactoryException();
        }
        
        $firstOperand = $this->operandCommandFactory->create($options['firstOperand']);
        $command = new OperationCommand($firstOperand);
        
        if (isset($options['otherOperands'])) {
            foreach ($options['otherOperands'] as $option) {
                if (isset($option['operator']) && isset($option['value'])) {
                    $command->addOperand(
                        $option['operator'], 
                        $this->operandCommandFactory->create($option['value'])
                    );
                }
            }
        }
        
        return $command;
    }
    
}
