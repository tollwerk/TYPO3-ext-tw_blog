# TYPO3-ext-tw_blog

Blog extension for TYPO3

## Documentation t.b.d.
 
* Basic feature
* Doktype
* BackendLayout
* Pagetree etc.


### Hooks
For a well defined behaviour, the classes you provide to the hooks must implement
corresponding interfaces. Also, this way you can see all available hooks by having a look
into `tw_blog/Classes/Hooks/TwBlog/`.

#### createQueryStatement

Use this to manipulate the QueryBuilder object created by BlogArticleRepository and provided to
all find-methods. You can, for example, add custom database restrictions or query constraints.

**Connect to the hook**

```php
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/tw_blog']['createQueryStatement'][] = \Vendor\YourNamespace\YourClass::class
```

**Use the hook**

```php

use Tollwerk\TwBlog\Hooks\TwBlog\CreateQueryStatementInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CreateQueryStatement implements CreateQueryStatementInterface
{

    /**
     * Hook for manipulating the central query builder
     * used by all repository find-methods.
     *
     * @param QueryBuilder $query
     */
    public function createQueryStatement(&$query): void
    {
        // Manipulate the QueryBuilder here
        // to add custom constraints etc.
    }
}
```

