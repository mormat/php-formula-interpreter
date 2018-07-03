<?php
/**
 * @author Petra Barus <petra.barus@gmail.com>
 */

namespace FormulaInterpreter\Command;

/**
 * @author Petra Barus <petra.barus@gmail.com>
 */
class StringCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $value;

    public function __construct($value)
    {
        if (!is_string($value)) {
            $message = sprintf(
                'Parameter $value of method __construct() of class %s must be an string. Got %s type instead.',
                get_class($this),
                gettype($value)
            );
            throw new \InvalidArgumentException($message);
        }

        $this->value = $value;
    }

    public function run()
    {
        return $this->value;
    }

    public static function create($options)
    {
    }

    public function getParameters()
    {
        return [];
    }
}