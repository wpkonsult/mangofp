#!/bin/bash
#Ensure that autoload is up-to date
rm -f.phpunit.result.cache
~/projektid/mangofp/test/phpunit --bootstrap ~/projektid/mangofp/autoload.php ~/projektid/mangofp/test