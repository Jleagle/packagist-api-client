# Packagist API Client

A small helper class to request package details from the Packagist API

#### Get a list of every package

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

#### Search for a package

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

#### Get a packages details

```php
$packagist = new Packagist();
$package = $packagist->package('jleagle', 'packagist-api-client');
```

#### Get the latest packages added

```php
$packagist = new Packagist();
$package = $packagist->latestAdded();
```

#### Get the latest package releases

```php
$packagist = new Packagist();
$package = $packagist->latestReleased();
```
