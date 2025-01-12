php-formula-interpreter
=======================

A stand-alone php library for parsing and running formulas

## Installation via composer

```bash
composer require mormat/php-formula-interpreter
```

## Usage

First, create an instance of `\Mormat\FormulaInterpreter\Compiler`

```php
$compiler = new \Mormat\FormulaInterpreter\Compiler();
```

Then use the `compile()`method to parse the formula you want to interpret. It will return an instance of `\Mormat\FormulaInterpreter\Executable` :

```php
$executable = $compiler->compile('2 + 2');
```

Finally run the formula from the executable :

```php
$result = $executable->run();
// $result equals 4
```

### Examples of formulas

```
// variables can be used
price + 2 

// parenthesis can be used
(1 + 2) * 3 

// default functions are available
sqrt(4) 

// complex formulas can be used
(((100 * 0.43075) * 1.1 * 1.5) / (1-0.425)) * 1.105 

// string are supported
lowercase('FOO')

// arrays are supported
count([2, 3, 4])

// custom functions can be registered
your_function_here(2) 

// use the `in` operator to check if an item is in array
1 in [1, 2, 3]  // returns true

// use the `in` operator to check if a substring is in a string
'Wars' in 'Star Wars'

// Logical operators (`and`, `or`, `not`)
1 < x and x < 10
1 < x or x < 10
not x > 0
```

## Supported types in formulas

### Numeric values

A numeric value can be an integer or a float

```
    2       // integer
    2.30    // float
```

### String values

Use simple quote to delimiter strings

```
    'foobar'
```

### Array values

Use comma to separate items and brackets to wrap the items
```
    [1, 2, 3]
```

Functions, strings and operations can be used as an item of an array
```
    [cos(0), 'foobar', 2 + 2]
```

## Using operators

The following operators are available :
| operator | usage | description |
|-|-|-|
| `+` | a + b | Sum of a and b. |
| `-` | a - b | Difference of a and b. |
| `*` | a * b | Product of a and b. |
| `/` | a / b | Quotient of a and b. |
| `in` | a in b | If a is an array, checks if b is an item of a. If a is a string, checks if b is a substring of a |

The operators `*`, `\` are being evaluated first, then the operators `+` and `-`

You can also force the prioriry of an expression by using parentheses like this

```
2 * (3 + 2)
```

You can use as many parentheses as you like.

```
2 * (2 * (3 + 2 * (3 + 2)) + 2)
```

## Using variables

A variable is just a word inside your formula like this :

```
price * rate / 100
```

Just before executing a formula in PHP, make sure to inject all the required variables in an array

```php
$variables = array(
   'price' => 40.2,
   'rate' => 12.8
);

$executable->run($variables);
```

## Using functions

### Availables functions

| name           | allowed types      | description                   |
|----------------|--------------------|-------------------------------|
| __pi__         |                    | Get value of pi |
| __cos__        | `numeric`          | Cosine |
| __sin__        | `numeric`          | Sine |
| __sqrt__       | `numeric`          | Square root |
| __pow__        | `numeric`,`numeric` | Exponential expression |
| __modulo__     | `numeric`,`numeric` | Remainder of first value divided by second value |
| __lowercase__  | `string` | Converts to a string lowercase |
| __uppercase__  | `string` | Converts to a string uppercase |
| __capitalize__ | `string` | Make a string's first character uppercase |
| __count__      | `string\|array` | If value is an array, count the items in the array. If value is a string, count the characters in the string |

### How to register a custom function ?

Use the `registerCustomFunction()` method in the `\Mormat\FormulaInterpreter\Compiler` class.

The custom function must implement the `\Mormat\FormulaInterpreter\Functions\FunctionInterface`. This interface contains the methods below :
- **getName()** returns the name of the function
- **supports($arguments)** returns true if the $arguments send to the function are valid.
- **execute($arguments)**  executes the function and returns the value.

## Why this library ?

Some user could wants to perform a simple calculation and being able to change it as much as he can. Before using a library, you could use the `eval` function. But this method has two major drawbacks :

- Security. A php script is being evaluated by the eval function. Php is a very powerful language, perhaps too powerful for a user especially when the user wants to inject malicious code.

- Complexity. Php is also complex for someone who doesn't understand programming language. It could be nice to interpret an excel-like formula instead.