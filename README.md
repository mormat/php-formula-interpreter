php-formula-interpreter
=======================

A stand-alone php library for parsing and running formulas

## How does it work ?

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

// use the in operator to check if an item is in array
1 in [1, 2, 3]  // returns true

// use the in operator to check if a substring is in a string
'Wars' in 'Star Wars'

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
    // Example

    [cos(0), 'foobar', 2 + 2]
```

## More information about formulas

[Using operators](docs/operators.md)

[Using functions](docs/functions.md)

[Using variables](docs/variables.md)

## Why this library ?

Some user could wants to perform a simple calculation and being able to change it as much as he can. Before using a library, you could use the `eval` function. But this method has two major drawbacks :

- Security. A php script is being evaluated by the eval function. Php is a very powerful language, perhaps too powerful for a user especially when the user wants to inject malicious code.

- Complexity. Php is also complex for someone who doesn't understand programming language. It could be nice to interpret an excel-like formula instead.