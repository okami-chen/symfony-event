# Laravel Symfony Event


## Molde 

```php

use OkamiChen\SymfonyEvent\FlowTrait;

protected $flowName = 'order';

```

## example

```yaml

places:
    - draft
    - review
    - rejected
    - published
    - deleted
    - hidden
transitions:
    review:
        from: draft
        to:   review
    publish:
        from: review
        to:   published
    reject:
        from: [review,draft]
        to:   [rejected]
    delete:
        from: published
        to:   deleted
    hidden:
        from: published
        to:   hidden
        
```
