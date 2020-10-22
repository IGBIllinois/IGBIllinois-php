# IGBIllinois-php

[![Build Status](https://www.travis-ci.com/IGBIllinois/IGBIllinois-php.svg?branch=main)](https://www.travis-ci.com/IGBIllinois/IGBIllinois-php)

Common PHP Libraries used in IGBIllinois projects

## Install
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
```
"igbillinois/igbillinois-php": "dev-main"
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
$db = \IGBIllinois\db(...);
$ldap = \IGBIllinois\ldap(...);
```

## API Documentation
* Documentation created using phpdocumentor [https://www.phpdoc.org/](https://www.phpdoc.org/)
* API Documentation located at [https://igbillinois.github.io/IGBIllinois-php/](https://igbillinois.github.io/IGBIllinois-php/)
* To regenerate documentation, run 
```
vendor/bin/phpdoc -d libs -t docs --template responsive --title 'IGBIllinois-php API'
```

