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

## API Documentation
* API Documentation located at [https://igbillinois.github.io/IGBIllinois-php/](https://igbillinois.github.io/IGBIllinois-php/)
* To regenerate documentation, run 
```
vendor/bin/phpdoc -d libs -t docs --template responsive
```

