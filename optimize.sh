#!/bin/bash

echo "Would you like to use sudo ? [y/n]"
read use_sudo

if [[ use_sudo == "y" ]]; then
    sudo php artisan config:clear
    sudo php artisan cache:clear
    sudo php artisan view:clear
    sudo php artisan optimize
    sudo composer dump-autoload

else
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan optimize
    composer dump-autoload

fi