# IGBIllinois-php

[![Build Status](https://github.com/IGBIllinois/IGBIllinois-php/actions/workflows/main.yml/badge.svg)](https://github.com/IGBIllinois/IGBIllinois-php/actions/workflows/main.yml)

Common PHP Libraries used in IGBIllinois projects

## Requirements
* Composer
* PHP 7.2 or greater
* PHP gd module
* PHP mbstring module
* PHP json module
* PHP ldap module

## Install
* Install required PHP modules
<pre>
dnf install php-gd php-mbstring php-ldap php-json
</pre>
* Add to your composer.json file
```
"repositories" : [
    {
        "type": "vcs",
        "url": "https://www.github.com/IGBIllinois/IGBIllinois-php"
    }
],
```
* Add to your 'required' section of the composer.json file
* For latest development code in the main brach
```
"igbillinois/igbillinois-php": "dev-main"
```
* For a tag released.  Latest releases are at [https://github.com/IGBIllinois/IGBIllinois-php/releases](https://github.com/IGBIllinois/IGBIllinois-php/releases)
```
"igbillinois/igbillinois-php": "1.0"
```
* To always use latest tag release
```
"igbillinois/igbillinois-php": ">=1.0 <2.0"
```
* Run composer install
```
composer install
```
* The namespace is IGBIllinois.  Add to your PHP code
```
use IGBIllinois;
```
or to call a class directly
```
$db = new \IGBIllinois\db(...);
$ldap = new \IGBIllinois\ldap(...);
```

## API Documentation
* Documentation created using phpdocumentor [https://www.phpdoc.org/](https://www.phpdoc.org/)
* API Documentation located at [https://igbillinois.github.io/IGBIllinois-php/](https://igbillinois.github.io/IGBIllinois-php/)
* To regenerate documentation, run 
```
vendor/bin/phpdoc -d libs -t docs --template responsive --title 'IGBIllinois-php API'
```

