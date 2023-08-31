#!/bin/bash

#php tools/phpunit.phar --bootstrap=tools/phpunit_bootstrap.php --include-path=../src $@
php tools/phpunit.phar --bootstrap=tools/phpunit_bootstrap.php --include-path=../src .

