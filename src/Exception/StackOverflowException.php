<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Throws if too many recursive calls
 *
 * @author mormat
 */
class StackOverflowException extends BaseException { }
