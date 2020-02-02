#!/bin/bash
#Ensure that autoload is up-to date
rm -f.phpunit.result.cache
~/projektid/peaches/test/phpunit --bootstrap ~/projektid/peaches/autoload.php ~/projektid/peaches/test