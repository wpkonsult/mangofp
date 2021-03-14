#!/bin/bash
#Ensure that autoload is up-to date
rm -f.phpunit.result.cache
./test/phpunit --bootstrap ./autoload.php ./test