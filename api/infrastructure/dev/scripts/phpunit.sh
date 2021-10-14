#!/bin/sh

debugFile=$PWD'/.dev/debug.html'

if [ $1 -gt 0 ]
then
        if [ $2 -gt 0 ]
        then
            rm $debugFile && vendor/phpunit/phpunit/phpunit --filter $3 >> $debugFile
            echo "Access this to browser: file:///var/www/personal/comment-project/infrastructure/dev/scripts/debug.html"
        else
            rm $debugFile && vendor/phpunit/phpunit/phpunit >> $debugFile
            echo "Access this to browser: file:///var/www/personal/comment-project/infrastructure/dev/scripts/debug.html"
        fi

else
        if [ $2 -gt 0 ]
        then
            vendor/phpunit/phpunit/phpunit --filter $3
        else
            vendor/phpunit/phpunit/phpunit
        fi
fi


