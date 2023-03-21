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