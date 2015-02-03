Packagist API Client
================

[![Build Status (Scrutinizer)](https://scrutinizer-ci.com/g/Jleagle/gazelle-api-client/badges/build.png)](https://scrutinizer-ci.com/g/Jleagle/gazelle-api-client)
[![Code Quality (scrutinizer)](https://scrutinizer-ci.com/g/Jleagle/gazelle-api-client/badges/quality-score.png)](https://scrutinizer-ci.com/g/Jleagle/gazelle-api-client)
[![Latest Stable Version](https://poser.pugx.org/Jleagle/gazelle-api-client/v/stable.png)](https://packagist.org/packages/Jleagle/gazelle-api-client)
[![Latest Unstable Version](https://poser.pugx.org/Jleagle/gazelle-api-client/v/unstable.png)](https://packagist.org/packages/Jleagle/gazelle-api-client)

This class allows you to retrieve information about packages from the Packagist "API".

###Get a list of every package

```php
$packagist = new Packagist();
$packages = $packagist->all();
```

This can return a lot of results and can be filtered like so:

```php
$packagist = new Packagist();
$zend_packages = $packagist->all('*zend*');
```

This field supports multiple wildcards and character classes.

### Search for a package

This will return the same paginated search results as on the Packagist website:

```php
$packagist = new Packagist();
$zend_packages = $packagist->search('zend');
```

You can also filter the results by supplying an array of tags:

```php
$packagist = new Packagist();
$zend_packages = $packagist->search('zend', ['zf2']);
```

The third parameter is the page number.

### Get a packages details

```php
$packagist = new Packagist();
$package = $packagist->package('jleagle', 'packagist-api-client');
```

