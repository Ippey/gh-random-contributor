# gh-random-contributor

A library for getting random GitHub contributors.

## Installation

```shell
composer require ippey/gh-random-contributor
```

## How to use

```php
<?php

require(__DIR__ . '/vendor/autoload.php');

use Ippey\GhRandomContributor\GhRandomContributor;
use Ippey\GhRandomContributor\GhRandomContributorFetchException;

$fetcher = GhRandomContributor::createFetcher();
try {
    $contributor = $fetcher->get('your-organization', 'your-repository');
} catch (GhRandomContributorFetchException $e) {
    // do something.
}
```

`GhRandomContributor::createFetcher()` creates a fetcher. This fetcher gets a specific repository's GitHub Contributor randomly via `get()` method.
`get()` method returns a GitHub contributor as `Ippey\GhRandomContributor\GhContributor`.

### GhContributor

| property | explanation | e.g. |
|-----|-----|-----|
| id | GitHub id | 12345 |
| username | GitHub username | Ippey |
| avatarUrl | Avatar URL | https://avatars.githubusercontent.com/u/471948?v=4 |
| thumbnailUrl | Thumbnail URL (80px) | https://avatars.githubusercontent.com/u/471948?v=4&s=80 |
| url | GitHub user page URL | https://github.com/Ippey |
| contributions | Contributions in the repository | 100 |

## License
MIT