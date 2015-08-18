#!/bin/bash

PHP_VERSION=`php-config --vernum`

if [ $TRAVIS_PHP_VERSION == 'hhvm' ] ;
then
    exit 0
fi

# Install APC for php > 5.5 and lower than 7
if [[ $PHP_VERSION -ge 50500 && $PHP_VERSION -lt 70000 ]] ;
then
    echo "--- Installing APC ---"

    echo "- Set Pecl to Beta -"
    pecl config-set preferred_state beta

    echo "- Update Pecl -"
    pecl channel-update pecl.php.net

    echo "- Install APC from Pecl -"
    yes "" | pecl install apcu
fi

# Include APC for php < 5.5, pecl auto includes
if [[ $PHP_VERSION -lt 50500 ]] ;
then
    echo "- Add apc.so to php.ini -"
    # Ensure a newline at the end of file
    echo '' >> ./tests/travis/php.ini
    echo 'extension="apc.so"' >> ./tests/travis/php.ini
fi

echo "- Loading custom php.ini -"
phpenv config-add ./tests/travis/php.ini