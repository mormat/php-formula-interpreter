# Using functions

Here is an example of formula using a function :

```
   cos(0)
```

Available functions : `pi`, `pow`, `cos`, `sin`, `sqrt` & `modulo`

You can embed functions as much as you like

```
   pow(sqrt(4), 2)
```

## Availables functions

| name           | allowed types      | description                   |
|----------------|--------------------|-------------------------------|
| __pi__         |                    | Get value of pi |
| __cos__        | `numeric`          | Cosine |
| __sin__        | `numeric`          | Sine |
| __sqrt__       | `numeric`          | Square root |
| __pow__        | `numeric`,`numeric` | Exponential expression |
| __modulo__     | `numeric`,`numeric` | Remainder of first value divided by second value |
| __lowercase__  | `string` | Converts to a string lowercase |
| __uppercase__  | `string` | Converts to a string uppercase |
| __capitalize__ | `string` | Make a string's first character uppercase |
| __count__      | `string\|array` | If value is an array, count the items in the array. If value is a string, count the characters in the string |

## How to register a custom function ?

Use the `registerCustomFunction()` method in the `\Mormat\FormulaInterpreter\Compiler` class.

The custom function must implement the `\Mormat\FormulaInterpreter\Functions\FunctionInterface'. This interface contains the methods below :
- **getName()** returns the name of the function
- **supports($arguments)** returns true if the $arguments send to the function are valid.
- **execute($arguments)**  executes the function and returns the value.
