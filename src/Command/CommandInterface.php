<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
interface CommandInterface {
    
    function run(CommandContext $context);
     
}
