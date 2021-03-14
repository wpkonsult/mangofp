#!/bin/bash
#To use the scipt install phpab in home folder:
#sudo wget -O phpab https://github.com/theseer/Autoload/releases/download/1.25.8/phpab-1.25.8.phar && chmod +x phpab
set -e

./run_tests.sh

echo 'Staging backend'
cp -rv ../mangofp/src ../mangofp/stage/
cp -rv ../mangofp/mangofp.php ../mangofp/stage/
cp -rv ../mangofp/autoload.php ../mangofp/stage/

echo 'Copying language resources'
mkdir -p  ../mangofp/stage/languages
cp -rv ../mangofp/languages/*.po ../mangofp/stage/languages/
cp -rv ../mangofp/languages/*.mo ../mangofp/stage/languages/
