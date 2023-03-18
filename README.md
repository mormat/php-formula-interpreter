php-formula-interpreter
=======================

A formula interpreter for php

# Why this library ?

Some user could wants to perform a simple calculation and being able to change it as much as he can. Before using a library, you could use the `eval` function. But this method has two major drawbacks :

- Security. A php script is being evaluated by the eval function. Php is a very powerful language, perhaps too powerful for a user especially when the user wants to inject malicious code.

- Complexity. Php is also complex for someone who doesn't understand programming language. It could be nice to interpret an excel-like formula instead.


# How does it work ?

First, create an instance of `FormulaInterpreter\Compiler`

```php
$compiler = new FormulaInterpreter\Compiler();
```

Then use the `compile()`method to parse the formula you want to interpret. It will return an instance of `FormulaInterpreter\Executable` :

```php
$executable = $compiler->compile('2 + 2');
```

Finally run the formula from the executable :

```php
$result = $executable->run();
// $result equals 4
```

# Using operators

Operator multiplication (*) and division (\) are being evaluted first, then addition (+) and subtraction (-)

You can also force the prioriry of an expression by using parentheses like this

```php
'2 * (3 + 2)'
```

You can use as many parentheses as you like.

```php
'2 * (2 * (3 + 2 * (3 + 2)) + 2)'
```

Others operators like modulo, power, etc. will be implemented in the future as functions.

# Using variables

A variable is just a word inside your formula like this :

```php
'price * rate / 100'
```

Just before executing a formula, make sure to inject all the required variables in an array

```
$variables = array(
   'price' => 40.2,
   'rate' => 12.8
);

$executable->run($variables);
```

# Using functions 

Here is an example of expression using a function :

```php
   'cos(0)'
```

Available functions : `pi`, `pow`, `cos`, `sin`, `sqrt` & `modulo`

You can embed functions as much as you like

```php
   'pow(sqrt(4), 2)'
```


