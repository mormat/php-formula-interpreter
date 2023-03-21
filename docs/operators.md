# Using operators

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

```php
'2 * (3 + 2)'
```

You can use as many parentheses as you like.

```php
'2 * (2 * (3 + 2 * (3 + 2)) + 2)'
```
