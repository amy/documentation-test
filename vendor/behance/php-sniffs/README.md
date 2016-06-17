Behance PHP_CodeSniffer Sniffs
==========

Something smells bad.

## To Run
```
phpcs --standard=/path/to/this/repo/Behance/ruleset.xml path/to/files
```

Or if it's installed in your installation of phpcs, just run
```
phpcs --standard=Behance path/to/files
```

## Contributing

### Testing

All changes need tests. **Code Coverage must remain at 100%**

```
cd /path/to/this/repo;
composer install;

vendor/bin/phpunit;
vendor/bin/phpunit --coverage-html=coverage;
```

### Pull Requests

All PRs need two thumbs before merge. Use the big green button on Github to merge once passing.

### Proposing New Sniffs

All new sniffs must be voted on at a backend team meeting. Unexpected, non-controversial edge cases can be discussed in Slack.

### Releasing

We follow semver. For linters, that means:

* Patch: changes that fix false positives.
* Minor: changes that fix false negatives, new rules.
* Major: changes that require upgrading major versions of PHPCS itself.

Create a release on github at https://github.com/behance/php-sniffs/releases. Ensure that you have the correct semver version number and an adequate title and description of the release.