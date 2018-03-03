# Simple expression grammar
It's a simple LL(1)-grammar[^1] that matches add/multiply expressions with parentheses, like `id*(id+id)`.
```
E  →  T E'
E' →  + T E' | ε
T  →  F T'
T' →  * F T' | ε
F  →  ( E ) | id
```
[^1]: Example 4.14 from: Aho, Lam, Sethi, Ullman, [_Compilers: Principles, Techniques, & Tools_](https://en.wikipedia.org/wiki/Compilers:_Principles,_Techniques,_and_Tools), Addison-Wesley, 2006. ISBN: 978-0321547989.